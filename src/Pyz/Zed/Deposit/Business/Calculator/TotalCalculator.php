<?php
/**
 * Durst - project - TotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.05.18
 * Time: 11:22
 */

namespace Pyz\Zed\Deposit\Business\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Pyz\Shared\Deposit\DepositConstants;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class TotalCalculator implements CalculatorInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $depositTotal = $this
                    ->calculateDepositTotalForItems($concreteTimeSlot->getExpenses());

                $concreteTimeSlot
                    ->getTotals()
                    ->setDepositTotal($depositTotal);
            }
        } else {
            $calculableObjectTransfer
                ->requireTotals();

            $depositTotal = $this
                ->calculateDepositTotalForItems($calculableObjectTransfer->getExpenses());

            $calculableObjectTransfer
                ->getTotals()
                ->setDepositTotal($depositTotal);
        }
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculateDisplayTotal(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $displayTotal = $concreteTimeSlot
                    ->getTotals()
                    ->getGrandTotal();

                foreach ($concreteTimeSlot->getExpenses() as $expense) {
                    if ($this->isDepositExpense($expense)) {
                        $this->assertExpenseRequirements($expense);
                        $displayTotal = $displayTotal - $expense->getSumPrice();
                    } else if ($this->isDepositReturnExpense($expense)) {
                        $this->assertExpenseRequirements($expense);
                        $displayTotal = $displayTotal + $expense->getSumPrice();
                    }
                }

                $concreteTimeSlot
                    ->getTotals()
                    ->setDisplayTotal($displayTotal);
            }
        } else {
            $displayTotal = $calculableObjectTransfer
                ->getTotals()
                ->getGrandTotal();

            foreach ($calculableObjectTransfer->getExpenses() as $expenseTransfer) {
                if ($this->isDepositExpense($expenseTransfer)) {
                    $this->assertExpenseRequirements($expenseTransfer);
                    $displayTotal = $displayTotal - $expenseTransfer->getSumPrice();
                } else if ($this->isDepositReturnExpense($expenseTransfer)) {
                    $this->assertExpenseRequirements($expenseTransfer);
                    $displayTotal = $displayTotal + $expenseTransfer->getSumPrice();
                }
            }

            $calculableObjectTransfer
                ->getTotals()
                ->setDisplayTotal($displayTotal);
        }
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculateWeightTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $weightTotal = $this
                    ->calculateWeightTotalForItems($concreteTimeSlot->getExpenses());

                $concreteTimeSlot
                    ->getTotals()
                    ->setWeightTotal($weightTotal);

            }
        } else {
            $calculableObjectTransfer
                ->requireTotals();

            $weightTotal = $this
                ->calculateWeightTotalForItems($calculableObjectTransfer->getExpenses());

            $calculableObjectTransfer
                ->getTotals()
                ->setWeightTotal($weightTotal);
        }
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    protected function assertRequirements(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $calculableObjectTransfer->requireTotals()->requireExpenses();
        $calculableObjectTransfer->getTotals()->requireGrandTotal();
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     */
    protected function assertExpenseRequirements(ExpenseTransfer $expenseTransfer)
    {
        $expenseTransfer->requireSumPrice();
    }

    /**
     * @param \ArrayObject|ExpenseTransfer[] $expenses
     * @return int
     */
    protected function calculateDepositTotalForItems($expenses) : int
    {
        $totalDepositAmount = 0;

        foreach ($expenses as $expense) {

            if($this->isDepositExpense($expense)){
                $this->assertExpenseRequirements($expense);
                $totalDepositAmount += $expense->getSumPrice();
            } else if($this->isDepositReturnExpense($expense)){
                $this->assertExpenseRequirements($expense);
                $totalDepositAmount -= $expense->getSumPrice();
            }
        }

        return $totalDepositAmount;
    }

    /**
     * @param \ArrayObject|ExpenseTransfer[] $expenses
     * @return int
     */
    protected function calculateWeightTotalForItems($expenses) : int
    {
        $totalWeightAmount = 0;

        foreach ($expenses as $expense) {

            if($this->isDepositExpense($expense)){
                $this->assertExpenseRequirements($expense);
                $totalWeightAmount += $expense->getSumWeight();
            }
        }

        return $totalWeightAmount;
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     * @return bool
     */
    protected function isDepositExpense(ExpenseTransfer $expenseTransfer) : bool
    {
        return (
            substr(
                $expenseTransfer->getType(),
                0,
                strlen(DepositConstants::DEPOSIT_EXPENSE_TYPE)
            ) === DepositConstants::DEPOSIT_EXPENSE_TYPE
        );
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     * @return bool
     */
    protected function isDepositReturnExpense(ExpenseTransfer $expenseTransfer) : bool
    {
        return (
            $expenseTransfer->getType() === DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE
        );
    }
}
