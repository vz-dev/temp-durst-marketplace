<?php
/**
 * Durst - project - SignatureManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 10:23
 */

namespace Pyz\Zed\Oms\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;

interface SignatureManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $signature
     */
    public function addSignatureToOrder(
        OrderTransfer $orderTransfer,
        string $signature
    ): void;
}