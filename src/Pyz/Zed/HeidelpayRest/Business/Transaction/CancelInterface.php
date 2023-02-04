<?php
/**
 * Durst - project - CancelInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.08.20
 * Time: 09:56
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;


use Generated\Shared\Transfer\OrderTransfer;

interface CancelInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function cancelPaymentForOrder(OrderTransfer $orderTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function cancelRemainingAmountForOrder(OrderTransfer $orderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelCancelableAmountForOrder(OrderTransfer $orderTransfer): void;
}
