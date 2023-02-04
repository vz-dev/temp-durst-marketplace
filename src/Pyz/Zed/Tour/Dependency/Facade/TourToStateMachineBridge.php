<?php
/**
 * Durst - project - TourStateMachineBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Dependency\Facade;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;

class TourToStateMachineBridge implements TourToStateMachineBridgeInterface
{
    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface
     */
    protected $stateMachineFacade;

    /**
     * TourToStateMachineBridge constructor.
     * @param \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface $stateMachineFacade
     */
    public function __construct(StateMachineFacadeInterface $stateMachineFacade)
    {
        $this->stateMachineFacade = $stateMachineFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param int $identifier
     * @return int
     */
    public function triggerForNewStateMachineItem(StateMachineProcessTransfer $stateMachineProcessTransfer, int $identifier): int
    {
        return $this
            ->stateMachineFacade
            ->triggerForNewStateMachineItem(
                $stateMachineProcessTransfer,
                $identifier
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $eventName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return int
     */
    public function triggerEvent(string $eventName, StateMachineItemTransfer $stateMachineItemTransfer): int
    {
        return $this
            ->stateMachineFacade
            ->triggerEvent(
                $eventName,
                $stateMachineItemTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $stateMachineItems
     * @return array
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array
    {
        return $this
            ->stateMachineFacade
            ->getManualEventsForStateMachineItems($stateMachineItems);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return array
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer): array
    {
        return $this
            ->stateMachineFacade
            ->getManualEventsForStateMachineItem($stateMachineItemTransfer);
    }
}
