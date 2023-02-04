<?php
/**
 * Durst - project - HeidelpayRestFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:13
 */

namespace Pyz\Zed\HeidelpayRest\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;
use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class HeidelpayRestFacade
 * @package Pyz\Zed\HeidelpayRest\Business
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestBusinessFactory getFactory()
 */
class HeidelpayRestFacade extends AbstractFacade implements HeidelpayRestFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     */
    public function capturePaymentForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createCaptureTransaction()
            ->capturePaymentForOrder($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @return array
     */
    public function refundPaymentForOrder(OrderTransfer $order): array
    {
        return $this
            ->getFactory()
            ->createRefundTransaction()
            ->refundPaymentForOrder($order);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return array
     */
    public function cancelPaymentForOrder(OrderTransfer $orderTransfer): array
    {
        return $this
            ->getFactory()
            ->createCancelTransaction()
            ->cancelPaymentForOrder(
                $orderTransfer
            );
    }

    /**
     * Specification:
     * - Saves order payment method data according to quote and checkout response transfer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void {
        $this
            ->getFactory()
            ->createOrderSaver()
            ->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * Specification:
     *  - Executes a post save hook for the following payment methods:
     *    Sofort / authorize: checks for an external redirect URL in transaction log and redirects customer to the payment system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer {
        return $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->postSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     */
    public function cancelAuthorization(int $idSalesOrder): void
    {
        $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->cancelAuthorization($idSalesOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     */
    public function authorizeOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->authorizeOrder($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isAuthorizationCompleted(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->isAuthorizationCompleted($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->isCaptureApproved($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $paymentId
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusByPaymentId(string $paymentId): HeidelpayRestAuthorizationTransfer
    {
        return $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->getAuthorizationStatusByPaymentId($paymentId);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $paymentId
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer
    {
        return $this
            ->getFactory()
            ->createAuthorizeTransaction()
            ->getAuthorizationStatusBySalesOrderRef($orderRef);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isRefundCompleted(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createRefundTransaction()
            ->isRefundCompleted($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function generateSepaSandboxPaymentTypeId(): string
    {
        return $this
            ->getFactory()
            ->createSepaType()
            ->generateSepaSandboxPaymentTypeId();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer|null
     */
    public function getHeidelpayRestLogByIdSalesOrderAndTransactionType(
        int $idSalesOrder,
        string $transactionType
    ): ?HeidelpayRestLogTransfer {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentLog()
            ->getHeidelpayRestLogByIdSalesOrderAndTransactionType(
                $idSalesOrder,
                $transactionType
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return array
     */
    public function createInvoice(OrderTransfer $orderTransfer): array
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->capturePaymentWithoutCancelForOrder($orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCapturePendingOrSuccess(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->isCapturePendingOrSuccess($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return array
     */
    public function getProcessingInformationForOrder(int $idSalesOrder): array
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->getProcessingInformationForOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     */
    public function createShipment(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createFinalizeTransaction()
            ->finalizePaymentForOrder($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function finalizePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createFinalizeTransaction()
            ->finalizePaymentWithoutCancelForOrder(
                $orderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isShipmentCompleted(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createFinalizeTransaction()
            ->isShipmentCompleted($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isCustomerValid(OrderTransfer $orderTransfer): bool
    {
        return $this
            ->getFactory()
            ->createCustomerValidator()
            ->isCustomerValid($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return HeidelpayRestLogTransfer[]
     */
    public function getHeidelpayRestLogsByIdSalesOrder(int $idSalesOrder): array
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentLog()
            ->getHeidelpayRestLogsByIdSalesOrder(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isCapturePendingOrRecoverableError(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->isCapturePendingOrRecoverableErrors(
                $orderTransfer,
                $paymentTypes
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isShipmentPendingOrRecoverableError(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool
    {
        return $this
            ->getFactory()
            ->createFinalizeTransaction()
            ->isShipmentPendingOrRecoverableErrors(
                $orderTransfer,
                $paymentTypes
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function captureChargeWithoutCancelForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createCaptureTransaction()
            ->captureChargeWithoutCancelForOrder(
                $orderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelRemainingAmountForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createCancelTransaction()
            ->cancelRemainingAmountForOrder(
                $orderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelCancelableAmountForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createCancelTransaction()
            ->cancelCancelableAmountForOrder(
                $orderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $transactionType
     * @return bool
     */
    public function isCaptureApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $transactionType
    ): bool
    {
        return $this
            ->getFactory()
            ->createCaptureTransaction()
            ->isCaptureApprovedForOrderAndTransactionType(
                $orderTransfer,
                $transactionType
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $type
     * @return bool
     */
    public function isShipmentApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $type
    ): bool
    {
        return $this
            ->getFactory()
            ->createFinalizeTransaction()
            ->isShipmentApprovedForOrderAndTransactionType(
                $orderTransfer,
                $type
            );
    }
}
