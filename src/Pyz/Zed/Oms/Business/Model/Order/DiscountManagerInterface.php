<?php
/**
 * Durst - project - DiscountManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.21
 * Time: 11:12
 */

namespace Pyz\Zed\Oms\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;

interface DiscountManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @param int $newAmount
     * @return void
     */
    public function setNewOrderDiscountAmount(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount,
        int $newAmount
    ): void;
}
