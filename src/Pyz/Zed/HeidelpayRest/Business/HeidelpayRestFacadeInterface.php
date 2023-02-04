<?php
/**
 * Durst - project - HeidelpayRestFacadeInterface.php.
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

interface HeidelpayRestFacadeInterface
{
    /**
     * Captures the payment via Heidelpay REST api.
     * Specification:
     *  - sends a capture request to heidelpay with payment reference in order
     *  - adds charge id to payment
     *  - adds data set to transaction log
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function capturePaymentForOrder(OrderTransfer $orderTransfer): void;

    /**
     * Refunds payment via Heidelpay REST api.
     * Specification:
     *  - sends a refund request to heidelpay with the authorization reference and
     *    the amount provided to the refund command.
     *  - adds data set to transaction log
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     *
     * @return array
     */
    public function refundPaymentForOrder(OrderTransfer $order): array;

    /**
     * Cancels payment via Heidelpay REST api
     * Specification:
     *  - sends a cancel request to heidelpay with the authorization reference and
     *    the amount provided.
     *  - adds data set to transaction log
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return array
     */
    public function cancelPaymentForOrder(OrderTransfer $orderTransfer): array;

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
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void;

    /**
     * Specification:
     *  - Executes a post save hook for the following payment methods:
     *    Sepa / authorize: checks for an external redirect URL in transaction log and redirects customer to the payment system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer;

    /**
     * Specification:
     *  - fetches the authorization from the heidelpay REST api
     *  - executes a cancel on the complete amount that was authorized
     *  - logs the transaction in the logging table
     *
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function cancelAuthorization(int $idSalesOrder): void;

    /**
     * Specification:
     *  - Checks whether there is a log entry with status
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *    and transaction type
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - Checks whether there is a log entry with status
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING
     *    and transaction type
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCapturePendingOrSuccess(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - authorizes the grand total of the latest order total with the payment type id
     *  - saves the payment id in the heidelpay rest payment
     *  - logs the transaction in the logging table
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function authorizeOrder(OrderTransfer $orderTransfer): void;

    /**
     * Specification:
     *  - receives the authorization object from the heidelpay REST api
     *  - checks the status of the authorization object
     *  - returns true if objects isSuccess()-Method is true and isPending() and isError() are false
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationCompleted(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - gets the authorization object from the heidelpay REST api via the payment id
     *  - hydrates the three status properties isSuccessful, isPending and isError
     *  - sets an error message for the customer
     *
     * @param string $paymentId
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusByPaymentId(string $paymentId): HeidelpayRestAuthorizationTransfer;

    /**
     * Specification:
     *  - gets the authorization object from the heidelpay REST api via the order reference
     *  - hydrates the three status properties isSuccessful, isPending and isError
     *  - sets an error message for the customer
     *
     * @param string $orderRef
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer;

    /**
     * Specification:
     *  - Checks whether there is a log entry with the type
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
     *    and the status
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundCompleted(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - communicates the grand total of the order to heidelpay
     *  - communicated invoice reference to heidelpay
     *  - returns an array with the bank information the invoice needs to contain
     *    e.g.
     *      [
     *          'holder' => 'Merchant Thuan',
     *          'iban' => 'DE89370400440532013000',
     *          'bic' => 'COBADEFFXXX',
     *          'descriptor' => '4018.9507.2850',
     *      ]
     *    The customer needs to pass the descriptor in the payment for heidelpay
     *    to identify the order
     *
     * @noinspection SpellCheckingInspection
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function createInvoice(OrderTransfer $orderTransfer): array;

    /**
     * Specification:
     *  - generates a payment type id for the payment type
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT
     *    for the sandbox system
     *  - should not be used in production!
     *  - The bank data used is IBAN DE89370400440532013000 as described in
     * @see https://docs.heidelpay.com/docs/testdata#section-sepa-direct-debit
     *
     * @return string
     */
    public function generateSepaSandboxPaymentTypeId(): string;

    /**
     * Specification:
     *  - receives the heidelpay payment id belonging to the order
     *  - fetches the payment object via the api
     *  - picks the first charge of the payment object and returns the
     *    processing info of this charge
     *  - throws @see \Pyz\Zed\HeidelpayRest\Business\Exception\PaymentWithoutChargesException
     *    if the payment object doesn't contain any charges
     *
     * @param int $idSalesOrder
     *
     * @return array
     */
    public function getProcessingInformationForOrder(int $idSalesOrder): array;

    /**
     * Specification:
     *  - Get a log entry by its foreign key to the sales table
     *  - Filter by a given transaction type
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer|null
     */
    public function getHeidelpayRestLogByIdSalesOrderAndTransactionType(
        int $idSalesOrder,
        string $transactionType
    ): ?HeidelpayRestLogTransfer;

    /**
     * Specification:
     *  - Checks whether there is a log entry with the type
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE
     *    and the status
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isShipmentCompleted(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - finalizes shipment of the order
     *  - sets the invoice reference in the shipment, so the reference needs
     *    to be set in the order transfer otherwise an exception is being thrown
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function createShipment(OrderTransfer $orderTransfer): void;

    /**
     * Specification:
     *  - finalizes shipment of the order without a cancellation beforehand
     *  - sets the invoice reference in the shipment, so the reference needs
     *    to be set in the order transfer otherwise an exception is being thrown
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function finalizePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): void;

    /**
     * Specification:
     *  - reads the customer id from the order transfer
     *  - fetches the matching customer object from heidelpay via api
     *  - returns true only if heidelpay returns a customer object with
     *    a billing address set
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCustomerValid(OrderTransfer $orderTransfer): bool;

    /**
     * Specification:
     *  - Get all log entries for a sales order by its id
     *
     * @param int $idSalesOrder
     * @return HeidelpayRestLogTransfer[]
     */
    public function getHeidelpayRestLogsByIdSalesOrder(
        int $idSalesOrder
    ): array;

    /**
     * Specification:
     *  - Checks whether there is a log entry with status
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING
     *    or
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR
     *    with
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_RECOVERABLE_ERRORS
     *    and transaction types
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isCapturePendingOrRecoverableError(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool;

    /**
     * Specification:
     *  - Checks whether there is a log entry with status
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING
     *    or
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR
     *    with
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_RECOVERABLE_ERRORS
     *    and transaction types
     *  - Returns false otherwise
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isShipmentPendingOrRecoverableError(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool;

    /**
     * Execute a charge for the given order and ignore a following cancel
     * This should fix the problem where the charge fails because of Core Timeout
     * And the whole grand total will be canceled afterwards
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function captureChargeWithoutCancelForOrder(OrderTransfer $orderTransfer): void;

    /**
     * Execute a cancel for the given order
     * Should only be called after a successful charge, so it won't
     * cancel the grand total for the order
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelRemainingAmountForOrder(OrderTransfer $orderTransfer): void;

    /**
     * Execute a cancel for the given order
     * Should be called after a successful finalize
     * Amount for cancellation will be calculated between payment and order
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelCancelableAmountForOrder(OrderTransfer $orderTransfer): void;

    /**
     * Checks, if a capture for the given order and a type is a success
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $transactionType
     * @return bool
     */
    public function isCaptureApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $transactionType
    ): bool;

    /**
     * Checks, if a heidelpay shipment for the given order and type is a success
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $type
     * @return bool
     */
    public function isShipmentApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $type
    ): bool;
}
