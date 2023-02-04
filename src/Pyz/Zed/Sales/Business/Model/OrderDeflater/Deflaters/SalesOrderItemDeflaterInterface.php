<?php
/**
 * Durst - project - SalesOrderItemDeflaterInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-04
 * Time: 14:42
 */

namespace Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters;


use Generated\Shared\Transfer\OrderTransfer;

interface SalesOrderItemDeflaterInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesOrderItems(OrderTransfer $orderTransfer) : OrderTransfer;
}