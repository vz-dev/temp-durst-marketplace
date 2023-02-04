<?php
/**
 * Durst - project - CustomerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.05.20
 * Time: 09:48
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction\Resource;


use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Resources\Customer as HeidelpayCustomer;

interface CustomerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\Customer|null
     */
    public function getCustomer(OrderTransfer $orderTransfer): ?HeidelpayCustomer;
}
