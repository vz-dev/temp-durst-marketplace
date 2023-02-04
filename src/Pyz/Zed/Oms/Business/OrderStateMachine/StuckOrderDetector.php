<?php

namespace Pyz\Zed\Oms\Business\OrderStateMachine;

use ArrayObject;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\Plugin\Mail\StuckOrdersNotificationMailTypePlugin;
use Pyz\Zed\Oms\OmsConfig;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\OrderStateMachine\FinderInterface;
use Spryker\Zed\Oms\Business\Process\StateInterface;
use Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class StuckOrderDetector implements StuckOrderDetectorInterface
{
    const START_DATE = '-1 day';
    const STUCK_THRESHOLD = '15 minutes';

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var OmsToMailInterface
     */
    protected $mailFacade;

    /**
     * @var OmsConfig
     */
    protected $config;

    /**
     * @var FinderInterface
     */
    protected $finder;

    public function __construct(
        SalesFacadeInterface $salesFacade,
        OmsToMailInterface $mailFacade,
        OmsConfig $config,
        FinderInterface $finder
    ) {
        $this->salesFacade = $salesFacade;
        $this->mailFacade = $mailFacade;
        $this->config = $config;
        $this->finder = $finder;
    }

    /**
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function detect(): void
    {
        $startDate = new DateTime(self::START_DATE);

        $problematicProcessStates = $this->getProblematicProcessStates();

        $problematicProcessNames = $this->getProblematicProcessNames($problematicProcessStates);
        $problematicStateNames = $this->getProblematicStateNames($problematicProcessStates);

        $orderItemsToCheck = $this
            ->salesFacade
            ->getOrderItemsByProcessesAndStates($startDate, $problematicProcessNames, $problematicStateNames);

        $newStuckOrderItems = $this->detectNewStuckOrderItems($orderItemsToCheck, $problematicProcessStates);

        if (count($newStuckOrderItems) > 0) {
            $this->markOrderItemsStuck($newStuckOrderItems);

            $newStuckOrderIds = $this->getOrderIdsFromItems($newStuckOrderItems);

            if (count($newStuckOrderIds) > 0) {
                $this->sendNewStuckOrdersNotificationMail($newStuckOrderIds);
            }
        }
    }

    /**
     * @param array|ItemTransfer[] $orderItems
     * @param array $problematicProcessStates
     * @return array|ItemTransfer[]
     */
    protected function detectNewStuckOrderItems(array $orderItems, array $problematicProcessStates): array
    {
        $newStuckOrderItems = [];

        foreach ($orderItems as $orderItem) {
            $processName = $orderItem->getProcess();

            $stateHistory = $orderItem->getStateHistory();

            /** @var ItemStateTransfer $currentState */
            $currentState = $stateHistory->offsetGet(count($stateHistory) - 1);

            if (in_array($currentState->getName(), $problematicProcessStates[$processName])) {
                $currentStateEnteredAt = new DateTime(
                    $currentState->getCreatedAt(),
                    new DateTimeZone('UTC')
                );

                $now = new DateTime('now');

                if ($currentStateEnteredAt->modify('+' . self::STUCK_THRESHOLD) < $now
                    && $orderItem->getIsStuck() !== true
                ) {
                    $newStuckOrderItems[] = $orderItem;
                }
            }
        }

        return $newStuckOrderItems;
    }

    /**
     * @return array
     */
    protected function getProblematicProcessStates(): array
    {
        $processes = $this
            ->finder
            ->getProcesses();

        $problematicProcessStates = [];

        foreach ($processes as $process) {
            foreach ($process->getAllTransitions() as $transition) {
                $event = $transition->getEvent();

                if ($event !== null && $event->isOnEnter()) {
                    $state = $transition->getSource();

                    /** @var StateInterface $problematicProcessState */
                    foreach ($problematicProcessStates as $problematicProcessName => $problematicProcess) {
                        foreach ($problematicProcess as $problematicStateName) {
                            if ($problematicProcessName === $process->getName() &&
                                $problematicStateName === $state->getName()
                            ) {
                                continue 2;
                            }
                        }
                    }

                    $problematicProcessStates[$process->getName()][] = $state->getName();
                }
            }
        }

        return $problematicProcessStates;
    }

    /**
     * @param array
     * @return array
     */
    protected function getProblematicProcessNames(array $problematicProcessStates): array
    {
        $problematicProcessNames = array_keys($problematicProcessStates);

        return $problematicProcessNames;
    }

    /**
     * @param array
     * @return array
     */
    protected function getProblematicStateNames(array $problematicProcessStates): array
    {
        $problematicStateNames = [];

        foreach ($problematicProcessStates as $problematicProcess) {
            foreach($problematicProcess as $problematicStateName) {
                if (in_array($problematicStateName, $problematicStateNames) === false) {
                    $problematicStateNames[] = $problematicStateName;
                }
            }
        }

        return $problematicStateNames;
    }

    /**
     * @param array|ItemTransfer[] $orderItems
     */
    protected function markOrderItemsStuck(array $orderItems)
    {
        $orderItemIds = array_map(function (ItemTransfer $orderItem) {
            return $orderItem->getIdSalesOrderItem();
        }, $orderItems);

        $this
            ->salesFacade
            ->setOrderItemsStuck($orderItemIds, true);
    }

    /**
     * @param array|ItemTransfer[] $orderItems
     * @return array
     */
    protected function getOrderIdsFromItems(array $orderItems): array
    {
        $orderIds = [];

        foreach ($orderItems as $orderItem) {
            $orderId = $orderItem->getFkSalesOrder();

            if (in_array($orderId, $orderIds) === false) {
                $orderIds[] = $orderId;
            }
        }

        return $orderIds;
    }

    /**
     * @param array $orderIds
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    protected function sendNewStuckOrdersNotificationMail(array $orderIds)
    {
        $newStuckOrders = $this
            ->salesFacade
            ->getMultipleOrdersByIdSalesOrders($orderIds);

        $developerRecipient = $this->config->getDeveloperMailRecipient();
        $serviceRecipient = $this->config->getServiceMailRecipient();

        $mailTransfer = (new MailTransfer())
            ->setType(StuckOrdersNotificationMailTypePlugin::NAME)
            ->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail($developerRecipient['email'])
                    ->setName($developerRecipient['name'])
            )
            ->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail($serviceRecipient['email'])
                    ->setName($serviceRecipient['name'])
            )
            ->setFridgeUrl($this->config->getFridgeBaseUrl())
            ->setNewStuckOrders(new ArrayObject($newStuckOrders));

        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }
}
