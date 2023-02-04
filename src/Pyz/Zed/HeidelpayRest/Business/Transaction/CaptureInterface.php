<?php
/**
 * Durst - project - CaptureInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:36
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\OrderTransfer;

interface CaptureInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\UnableToChargeException
     * @throws \RuntimeException
     */
    public function capturePaymentForOrder(OrderTransfer $order): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer): bool;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCapturePendingOrSuccess(OrderTransfer $orderTransfer): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return array
     */
    public function getProcessingInformationForOrder(int $idSalesOrder): array;

    /**
     * Executes a charge with the grand total of the given order.
     * This is useful if the grand total is final at the point of charge.
     * If processing information is present in the response it will be returned
     * e.g. invoice
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function capturePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): array;

    /**
     * Checks, if the given order transfer is either in pending state
     * or a recoverable error has occured, e.g.
     * - core timeout
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isCapturePendingOrRecoverableErrors(
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
     * Checks, if a capture for the given order and a type is a success
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
     *
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
     * @see \Pyz\Shared\HeidelpayRest\HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $type
     * @return bool
     */
    public function isCaptureApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $type
    ): bool;
}
