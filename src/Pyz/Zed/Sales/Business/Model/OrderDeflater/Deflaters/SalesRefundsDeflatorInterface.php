<?php
/**
 * Durst - project - SalesRefundsDeflatorInterface.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-04
 * Time: 14:42
 */

namespace Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters;


use Generated\Shared\Transfer\OrderTransfer;

interface SalesRefundsDeflatorInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesRefunds(OrderTransfer $orderTransfer) : OrderTransfer;
}