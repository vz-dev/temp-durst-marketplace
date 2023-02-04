<?php
/**
 * Durst - project - ExpenseTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 16:29
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\ExpenseTotalCalculator as SprykerExpenseTotalCalculator;

class ExpenseTotalCalculator extends SprykerExpenseTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $expenseTotal = $this
                    ->calculateExpenseTotalSumPrice($concreteTimeSlot->getExpenses());

                $concreteTimeSlot
                    ->getTotals()
                    ->setExpenseTotal($expenseTotal);
            }
        } else {
            $calculableObjectTransfer
                ->requireTotals();

            $expenseTotal = $this
                ->calculateExpenseTotalSumPrice($calculableObjectTransfer->getExpenses());

            $calculableObjectTransfer
                ->getTotals()
                ->setExpenseTotal($expenseTotal);
        }
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     *
     * @return int
     */
    protected function calculateExpenseTotalSumPrice(ArrayObject $expenses)
    {
        $expenseTotal = 0;
        foreach ($expenses as $expenseTransfer) {
            if($expenseTransfer->getIsNegative() === true){
                $expenseTotal -= $expenseTransfer->getSumPrice();
            } else {
                $expenseTotal += $expenseTransfer->getSumPrice();
            }
        }
        return $expenseTotal;
    }
}
