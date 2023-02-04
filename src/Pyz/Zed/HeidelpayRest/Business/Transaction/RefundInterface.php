<?php


/**
 * Durst - project - RefundInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:36
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\OrderTransfer;

interface RefundInterface
{
    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     *
     * @return array
     */
    public function refundPaymentForOrder(OrderTransfer $orderTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundCompleted(OrderTransfer $orderTransfer): bool;
}
