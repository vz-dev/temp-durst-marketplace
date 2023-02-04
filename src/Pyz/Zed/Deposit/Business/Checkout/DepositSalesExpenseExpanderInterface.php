<?php
/**
 * Durst - project - DepositSalesExpenseExpanderInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-03
 * Time: 22:18
 */

namespace Pyz\Zed\Deposit\Business\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;

interface DepositSalesExpenseExpanderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function expandDepositSaleExpense(QuoteTransfer $quoteTransfer) : QuoteTransfer;
}