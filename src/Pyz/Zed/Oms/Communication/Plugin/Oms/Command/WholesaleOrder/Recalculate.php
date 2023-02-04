<?php
/**
 * Durst - project - Recalculate.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.07.18
 * Time: 15:22
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class Recalculate
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 * @method OmsFacadeInterface getFacade()
 */
class Recalculate extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'recalculate';

    public const DATA_BRANCH_KEY = 'branch';

    public const ORDER_ITEM_STATES = [
        MarkDeliver::STATE_NAME,
        MarkDecline::STATE_NAME,
        MarkDamage::STATE_NAME,
        MarkLose::STATE_NAME
    ];

    /**
     *
     * Command which is executed per order basis
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException
     * @api
     *
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $oldStates = $this->isOldWholeSaleProcess($orderEntity);

        if (!$oldStates === true) {
            if (!$this->checkIfFirstRun($orderEntity)) {
                return [];
            }
        }

        $this
            ->getFactory()
            ->getOmsQueryContainer()
            ->getConnection()
            ->beginTransaction();

        try {
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

            $orderTransfer->setIsReseller($data[OrderRefundReturnDepositFormDataTransfer::IS_RESELLER]);
            $orderTransfer->setSignedAt($data[OrderRefundReturnDepositFormDataTransfer::SIGNED_AT]);
            $orderTransfer->setExternalAmountPaid($data[OrderRefundReturnDepositFormDataTransfer::EXTERNAL_AMOUNT_PAID]);

            $this
                ->getFacade()
                ->addSignatureToOrder(
                    $orderTransfer,
                    $data[OrderRefundReturnDepositFormDataTransfer::SIGNATURE]
                );

            $this
                ->getFacade()
                ->addExpensesToOrder(
                    $orderTransfer,
                    $data[static::DATA_BRANCH_KEY],
                    $data[OrderRefundReturnDepositFormDataTransfer::RETURN_DEPOSITS],
                    (int)($data[OrderRefundReturnDepositFormDataTransfer::REFUND]),
                    $data[OrderRefundReturnDepositFormDataTransfer::REFUND_COMMENT]
                );

            $refundItems = [];

            foreach ($orderEntity->getItems() as $item) {
                /** @var \Generated\Shared\Transfer\ItemTransfer $formItem */
                foreach ($data[OrderRefundReturnDepositFormDataTransfer::ITEMS] as $formItem) {
                    if ($item->getIdSalesOrderItem() === $formItem->getIdSalesOrderItem() && $item->getQuantity() !== $formItem->getQuantity()) {
                        $refundQuantity = $item->getQuantity() - $formItem->getQuantity();

                        if ($refundQuantity > $item->getQuantity()) {
                            $refundQuantity = $item->getQuantity();
                        }

                        $refundItems[$item->getIdSalesOrderItem()] = $refundQuantity;
                    }
                }
            }
            if (!empty($refundItems) === true) {
                if ($oldStates === true) {

                    $this
                        ->getFacade()
                        ->addRefundsToOrder(
                            $orderEntity,
                            $data[static::DATA_BRANCH_KEY],
                            $refundItems
                        );
                } else {
                    $this
                        ->getFacade()
                        ->addExpandedItemsRefundsToOrder(
                            $orderEntity,
                            $data[static::DATA_BRANCH_KEY],
                            $refundItems
                        );
                }
            }

        } catch (GrandTotalIsNegativeException $grandTotalIsNegativeException) {
            $this
                ->getFactory()
                ->getOmsQueryContainer()
                ->getConnection()
                ->rollBack();

            throw $grandTotalIsNegativeException;
        }

        $this
            ->getFactory()
            ->getOmsQueryContainer()
            ->getConnection()
            ->commit();

        try {
            $stateMachineItemTransfer = $this
                ->createStateMachineItemTransfer(
                    $orderTransfer->requireIdTourItemState()->getIdTourItemState(),
                    $orderTransfer->requireFkTour()->getFkTour(),
                    $orderTransfer->getIdSalesOrder()
                );
            $this
                ->getFactory()
                ->getStateMachineFacade()
                ->triggerEvent(
                    TourConstants::TOUR_STATE_EVENT_FINISH_DELIVERY,
                    $stateMachineItemTransfer
                );
        } catch (RequiredTransferPropertyException $e) {
            //if no tour id is set we don't need to trigger an event change
        }

        return [];
    }

    /**
     * @param SpySalesOrder $orderEntity
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function checkIfFirstRun(SpySalesOrder $orderEntity): bool
    {
        foreach ($orderEntity->getItems() as $item)
        {
            if(!in_array($item->getState()->getName(), self::ORDER_ITEM_STATES) === true){
                return false;
            }
        }
        return true;
    }

    /**
     * @param SpySalesOrder $order
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function isOldWholeSaleProcess(SpySalesOrder $order) : bool
    {
        foreach ($order->getItems() as $item){
            if(in_array($item->getProcess()->getName(), $this->getFactory()->getConfig()->getOldWholesaleProcess()) === true){
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $idItemState
     * @param int $idTour
     * @param int $idOrder
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(int $idItemState, int $idTour, int $idOrder): StateMachineItemTransfer
    {
        return (new StateMachineItemTransfer())
            ->setIdentifier($idTour)
            ->setIdItemState($idItemState)
            ->setTriggeringOrderId($idOrder);
    }
}
