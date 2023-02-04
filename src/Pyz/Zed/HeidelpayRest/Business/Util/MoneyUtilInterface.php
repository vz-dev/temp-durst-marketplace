<?php
/**
 * Durst - project - MoneyUtilInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.01.19
 * Time: 11:21
 */

namespace Pyz\Zed\HeidelpayRest\Business\Util;

use Generated\Shared\Transfer\OrderTransfer;

interface MoneyUtilInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     *
     * @return float
     */
    public function getDecimalGrandTotalForOrder(OrderTransfer $order): float;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    public function getGrandTotalForOrder(OrderTransfer $orderTransfer): int;

    /**
     * @param int $amount
     *
     * @return float
     */
    public function getFloatFromInt(int $amount): float;

    /**
     * @param float $amount
     *
     * @return int
     */
    public function getIntFromFloat(float $amount): int;

    /**
     * @param float $alreadyCharged
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return int
     */
    public function getCancelableAmountForOrder(float $alreadyCharged, OrderTransfer $orderTransfer): int;
}
