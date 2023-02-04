<?php
/**
 * Durst - project - DriverManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.07.20
 * Time: 13:41
 */

namespace Pyz\Zed\Oms\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;

interface DriverManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idDriver
     */
    public function addDriverToOrder(
        OrderTransfer $orderTransfer,
        int $idDriver
    ): void;
}
