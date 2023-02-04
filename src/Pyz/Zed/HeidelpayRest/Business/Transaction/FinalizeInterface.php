<?php
/**
 * Durst - project - FinalizeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 16:49
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;


use Generated\Shared\Transfer\OrderTransfer;

interface FinalizeInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function finalizePaymentForOrder(OrderTransfer $orderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function finalizePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isShipmentCompleted(OrderTransfer $orderTransfer): bool;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isShipmentPendingOrRecoverableErrors(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $type
     * @return bool
     */
    public function isShipmentApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $type
    ): bool;
}
