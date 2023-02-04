<?php
/**
 * Durst - project - SalesExpenseDepositDeflatorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-09-11
 * Time: 00:34
 */

namespace Pyz\Zed\Deposit\Business\Sales;


use Generated\Shared\Transfer\OrderTransfer;

interface SalesExpenseDepositDeflatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function deflateSalesExpenses(OrderTransfer $orderTransfer) : OrderTransfer;
}