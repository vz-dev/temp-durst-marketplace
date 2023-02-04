<?php
/**
 * Durst - project - CheckerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.03.19
 * Time: 11:39
 */

namespace Pyz\Zed\Deposit\Business\Order;


interface CheckerInterface
{
    /**
     * @param int $idSalesOrder
     * @return bool
     */
    public function hasOrderDepositReturns(int $idSalesOrder): bool;
}