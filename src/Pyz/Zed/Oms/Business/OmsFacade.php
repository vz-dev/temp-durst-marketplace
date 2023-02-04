<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 24.01.18
 * Time: 10:53
 */

namespace Pyz\Zed\Oms\Business;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\DurstCompanyTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Driver\Business\Model\Driver;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\OmsFacade as SprykerOmsFacade;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class OmsFacade
 * @package Pyz\Zed\Oms\Business
 * @method OmsBusinessFactory getFactory()
 */
class OmsFacade extends SprykerOmsFacade implements OmsFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function createInvoice(int $idSalesOrder): bool
    {
        return $this
            ->getFactory()
            ->createInvoiceManager()
            ->addInvoiceNumberToOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param string $mailType
     * @param \Generated\Shared\Transfer\MailTransfer|null $mailTransfer
     */
    public function sendInvoiceMail(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer,
        string $mailType,
        ?MailTransfer $mailTransfer = null
    ) {
        $this
            ->getFactory()
            ->createInvoiceMailManager()
            ->sendMail($orderTransfer, $branchTransfer, $mailType, $mailTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer
    {
        return $this
            ->getFactory()
            ->createDurstCompanyDetailsManager()
            ->createDurstCompanyTransfer();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return void
     */
    public function sendRefundMail(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer,
        DriverTransfer $driverTransfer
    ) {
        $this
            ->getFactory()
            ->createRefundMailManager()
            ->sendMail($orderTransfer, $branchTransfer, $driverTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param array $depositQuantities
     * @param int $refund
     * @param string $refundComment
     * @return bool
     */
    public function addExpensesToOrder(
        OrderTransfer $order,
        BranchTransfer $branchTransfer,
        array $depositQuantities,
        int $refund,
        string $refundComment
    ): bool {
        return $this
            ->getFactory()
            ->createExpenseManager()
            ->addReturnDepositRefundExpenseToOrder(
                $order,
                $branchTransfer,
                $depositQuantities,
                $refund,
                $refundComment
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param array $refundQuantities
     */
    public function addRefundsToOrder(
        SpySalesOrder $order,
        BranchTransfer $branchTransfer,
        array $refundQuantities
    ) {
        $this
            ->getFactory()
            ->createRefundManager()
            ->addRefundsToOrder(
                $order,
                $branchTransfer,
                $refundQuantities
            );
    }

    /**
     * @param SpySalesOrder $order
     * @param BranchTransfer $branchTransfer
     * @param array $refundQuantities
     * @throws Exception\RefundNegativeQuantityException
     * @throws Exception\RefundQuantityGreaterThanOrderQuantityException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Product\Business\Exception\MissingProductException
     */
    public function addExpandedItemsRefundsToOrder(SpySalesOrder $order, BranchTransfer $branchTransfer, array $refundQuantities)
    {
        $this
            ->getFactory()
            ->createRefundExpandedItemsManager()
            ->addRefundsToOrder($order, $branchTransfer, $refundQuantities);
    }

    /**
     * @return void
     */
    public function addSignatureToOrder(
        OrderTransfer $orderTransfer,
        string $signature
    ): void {
        $this
            ->getFactory()
            ->createSignatureManager()
            ->addSignatureToOrder($orderTransfer, $signature);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idDriver
     */
    public function addDriverToOrder(OrderTransfer $orderTransfer, int $idDriver): void
    {
        $this
            ->getFactory()
            ->createDriverManager()
            ->addDriverToOrder(
                $orderTransfer,
                $idDriver
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return \DateTime|null
     */
    public function getDeliveryTimeFromTransitionLogByIdSalesOrder(
        int $idSalesOrder
    ): ?DateTime
    {
        return $this
            ->getFactory()
            ->createTransitionLogModel()
            ->getDeliveryTimeFromTransitionLogByIdSalesOrder(
                $idSalesOrder
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @param BillingPeriodTransfer $billingPeriodTransfer
     */
    public function sendBillingMail(BranchTransfer $branchTransfer, BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $this
            ->getFactory()
            ->createBillingMailManager()
            ->sendMail($branchTransfer, $billingPeriodTransfer);
    }

    /**
     * {@inheritDoc}
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @param int $newAmount
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function setNewOrderDiscountAmount(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount,
        int $newAmount
    ): void
    {
        $this
            ->getFactory()
            ->createDiscountManager()
            ->setNewOrderDiscountAmount(
                $orderTransfer,
                $idSalesDiscount,
                $newAmount
            );
    }

    /**
     * {@inheritDoc}
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function getSplitExpensesRefundsReturnDepositsFromOrder(OrderTransfer $orderTransfer)  : array
    {
        return $this
            ->getFactory()
            ->createInvoiceMailManager()
            ->getSplitExpensesRefundsReturnDepositsFromOrder($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function detectStuckOrders(): void
    {
        $this
            ->getFactory()
            ->createStuckOrderDetector()
            ->detect();
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getOrderItemMatrix(): array
    {
        return $this
            ->getFactory()
            ->createUtilOrderItemMatrix()
            ->getMatrix();
    }
}
