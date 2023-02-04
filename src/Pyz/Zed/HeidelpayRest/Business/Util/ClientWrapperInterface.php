<?php
/**
 * Durst - project - ClientWrapperInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.05.20
 * Time: 09:50
 */

namespace Pyz\Zed\HeidelpayRest\Business\Util;


use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Heidelpay;

interface ClientWrapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Heidelpay
     */
    public function getHeidelpayClient(OrderTransfer $orderTransfer): Heidelpay;
}
