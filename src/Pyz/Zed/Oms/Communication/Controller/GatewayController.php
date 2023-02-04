<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 22:43
 */

namespace Pyz\Zed\Oms\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException;
use Pyz\Zed\Oms\Business\Exception\DiscountException;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDamage;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDecline;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDeliver;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkLose;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\Recalculate;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * Class GatewayController
 * @package Pyz\Zed\Oms\Communication\Controller
 * @method OmsFacadeInterface getFacade()
 * @method OmsCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param OrderRefundReturnDepositFormDataTransfer $dataTransfer
     * @return OrderRefundReturnDepositFormDataTransfer
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function triggerRecalculateEventForOrderItemsAction(OrderRefundReturnDepositFormDataTransfer $dataTransfer): OrderRefundReturnDepositFormDataTransfer
    {
        $branchTransfer = $this->getBranchById($dataTransfer->getBranch());

        $orderTransfer = $this->getFactory()->getSalesFacade()->getOrderByIdSalesOrder($dataTransfer->getOrigOrderItems()[0]->getFkSalesOrder());

        $data = $this->createRefundReturnDepositFormArray($dataTransfer, $branchTransfer);

        $this->addSignatureToOrder($orderTransfer, $data);

        $this
            ->addDriverToOrder(
                $orderTransfer,
                $data
            );

        if (
            $dataTransfer->getVoucher() !== null &&
            $dataTransfer->getVoucher()->getIdSalesDiscount() !== null &&
            $dataTransfer->getVoucher()->getAmount() !== null
        ) {
            try {
                $this
                    ->getFacade()
                    ->setNewOrderDiscountAmount(
                        $orderTransfer,
                        $dataTransfer->getVoucher()->getIdSalesDiscount(),
                        $dataTransfer->getVoucher()->getAmount()
                    );
            } catch (DiscountException $discountException) {
                $dataTransfer
                    ->setHasError(true)
                    ->setErrorMessage(
                        $discountException
                            ->getMessage()
                    );

                return $dataTransfer;
            }
        }

        try {
            $this
                ->getFacade()
                ->addExpensesToOrder(
                    $orderTransfer,
                    $data[Recalculate::DATA_BRANCH_KEY],
                    $data[OrderRefundReturnDepositFormDataTransfer::RETURN_DEPOSITS],
                    (int)($data[OrderRefundReturnDepositFormDataTransfer::REFUND]),
                    $data[OrderRefundReturnDepositFormDataTransfer::REFUND_COMMENT]
                );

            if(!$this->isOldWholeSaleProcess($orderTransfer) === true){
                $this->triggerEventForItems(MarkDecline::EVENT_ID, $dataTransfer->getItemsDeclined(), $data);
                $this->triggerEventForItems(MarkDamage::EVENT_ID, $dataTransfer->getItemsDamaged(), $data);
                $this->triggerEventForItems(MarkLose::EVENT_ID, $dataTransfer->getItemsLost(), $data);
                $this->triggerEventForItems(MarkDeliver::EVENT_ID, $dataTransfer->getItemsDelivered(), $data);
            }

            $this->triggerEventForItems(Recalculate::EVENT_ID, $dataTransfer->getOrigOrderItems(), $data);

            if ($orderTransfer->getGmStartTime() !== null) {
                $graphmastersOrderTransfer = $this
                    ->getFactory()
                    ->getGraphMastersFacade()
                    ->getOrderByReference($orderTransfer->getOrderReference());

                $this->markGraphmastersOrderFinished($graphmastersOrderTransfer);
            }
        } catch (GrandTotalIsNegativeException $grandTotalIsNegativeException) {
            $dataTransfer
                ->setHasError(true)
                ->setErrorMessage($grandTotalIsNegativeException->getMessage());
        }

        return $dataTransfer;
    }

    /**
     * @param ArrayObject|ItemTransfer $orderItems
     *
     * @return int[]
     */
    protected function getIdArrayFromOrderItemTransferArray(ArrayObject $orderItems): array
    {
        $orderIds = [];

        /** @var ItemTransfer $orderItem */
        foreach ($orderItems as $orderItem) {
            $orderIds[] = $orderItem->getIdSalesOrderItem();
        }

        return $orderIds;
    }

    /**
     * @param int $idBranch
     *
     * @return BranchTransfer
     */
    protected function getBranchById(int $idBranch): BranchTransfer
    {
        return $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($idBranch);
    }

    /**
     * @param OrderRefundReturnDepositFormDataTransfer $formData
     * @param OrderTransfer $origOrder
     * @param BranchTransfer $branchTransfer
     *
     * @return array
     */
    protected function createRefundReturnDepositFormArray(OrderRefundReturnDepositFormDataTransfer $formData, BranchTransfer $branchTransfer): array
    {
        return [
            Recalculate::DATA_BRANCH_KEY => $branchTransfer,
            OrderRefundReturnDepositFormDataTransfer::ORIG_ORDER_ITEMS => $formData->getOrigOrderItems(),
            OrderRefundReturnDepositFormDataTransfer::ITEMS => $formData->getItems(),
            OrderRefundReturnDepositFormDataTransfer::ITEMS_DELIVERED => $formData->getItemsDelivered(),
            OrderRefundReturnDepositFormDataTransfer::ITEMS_DAMAGED => $formData->getItemsDamaged(),
            OrderRefundReturnDepositFormDataTransfer::ITEMS_DECLINED => $formData->getItemsDeclined(),
            OrderRefundReturnDepositFormDataTransfer::ITEMS_LOST => $formData->getItemsLost(),
            OrderRefundReturnDepositFormDataTransfer::RETURN_DEPOSITS => (array)$formData->getReturnDeposits(),
            OrderRefundReturnDepositFormDataTransfer::REFUND => $formData->getRefund(),
            OrderRefundReturnDepositFormDataTransfer::REFUND_COMMENT => $formData->getRefundComment(),
            OrderRefundReturnDepositFormDataTransfer::SIGNATURE => $formData->getSignature(),
            OrderRefundReturnDepositFormDataTransfer::DRIVER => $formData->getDriver(),
            OrderRefundReturnDepositFormDataTransfer::IS_RESELLER => $formData->getIsReseller(),
            OrderRefundReturnDepositFormDataTransfer::SIGNED_AT => $formData->getSignedAt(),
            OrderRefundReturnDepositFormDataTransfer::EXTERNAL_AMOUNT_PAID => $formData->getExternalAmountPaid(),
        ];
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param array $data
     */
    protected function addSignatureToOrder(OrderTransfer $orderTransfer, array $data) : void
    {
        $this
            ->getFacade()
            ->addSignatureToOrder(
                $orderTransfer,
                $data[OrderRefundReturnDepositFormDataTransfer::SIGNATURE]
            );
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param array $data
     * @return void
     */
    protected function addDriverToOrder(
        OrderTransfer $orderTransfer,
        array $data
    ): void
    {
        $this
            ->getFacade()
            ->addDriverToOrder(
                $orderTransfer,
                $data[OrderRefundReturnDepositFormDataTransfer::DRIVER]
            );
    }

    /**
     * @param string $eventId
     * @param ArrayObject $orderItems
     * @param array $data
     */
    protected function triggerEventForItems(string $eventId, ArrayObject $orderItems, array $data)
    {
        $this
            ->getFacade()
            ->triggerEventForOrderItems(
                $eventId,
                $this->getIdArrayFromOrderItemTransferArray($orderItems),
                $data
            );
    }

    /**
     * @param OrderTransfer $order
     * @return bool
     */
    protected function isOldWholeSaleProcess(OrderTransfer $order) : bool
    {
        foreach ($order->getItems() as $item){
            if(in_array($item->getProcess(), $this->getFactory()->getConfig()->getOldWholesaleProcess()) === true){
                return true;
            }
        }

        return false;
    }

    /**
     * @param GraphMastersOrderTransfer $graphmastersOrderTransfer
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function markGraphmastersOrderFinished(GraphMastersOrderTransfer $graphmastersOrderTransfer): void
    {
        $this
            ->getFactory()
            ->getGraphMastersFacade()
            ->markOrderFinishedByReference($graphmastersOrderTransfer->getFkOrderReference());
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    protected function doesBranchUseGraphmasters(BranchTransfer $branchTransfer): bool
    {
       return $this
           ->getFactory()
           ->getGraphMastersFacade()
           ->doesBranchUseGraphmasters($branchTransfer->getIdBranch());
    }
}
