<?php
/**
 * Durst - project - DepositSalesExpenseExpanderander.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-03
 * Time: 11:40
 */

namespace Pyz\Zed\Deposit\Business\Checkout;


use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class DepositSalesExpenseExpander implements DepositSalesExpenseExpanderInterface
{
    public const DEPOSIT_EXPENSE_TYPE_PREFIX = 'deposit';

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function expandDepositSaleExpense(QuoteTransfer $quoteTransfer) : QuoteTransfer
    {
        $expenseTransfers = [];

        foreach($quoteTransfer->getExpenses() as $expense){

            if($this->typeStartsWithDepositTypePrefix($expense->getType())){
                $quantity = $expense->getQuantity();

                for ($i = 1; $quantity >= $i; $i++) {
                    $expenseTransfer = new ExpenseTransfer();
                    $expenseTransfer->fromArray($expense->toArray());
                    $expenseTransfer->setQuantity(1);
                    $expenseTransfer->setType(sprintf('%s-%d', $expense->getType(), $i));
                    $expenseTransfers[] = $expenseTransfer;
                }
            }else{
                $expenseTransfers[] = $expense;
            }
        }

        $quoteTransfer->setExpenses(new \ArrayObject($expenseTransfers));

        return $quoteTransfer;
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function typeStartsWithDepositTypePrefix(string $type) : bool
    {
        return strpos($type, static::DEPOSIT_EXPENSE_TYPE_PREFIX) === 0;
    }
}
