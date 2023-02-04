<?php
/**
 * Durst - project - OrderHydratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-25
 * Time: 20:22
 */

namespace Pyz\Zed\Integra\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;

interface OrderUpdaterInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     * @param array $orderData
     * @return OrderTransfer
     */
    public function updateOrderTransferWithIntegraData(OrderTransfer $orderTransfer, array &$orderData) : OrderTransfer;
}
