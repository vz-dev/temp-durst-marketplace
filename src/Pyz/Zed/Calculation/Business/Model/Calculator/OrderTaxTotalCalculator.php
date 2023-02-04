<?php
/**
 * Durst - project - OrderTaxTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 17:19
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\OrderTaxTotalCalculator as SprykerOrderTaxTotalCalculator;

class OrderTaxTotalCalculator extends SprykerOrderTaxTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $totalTaxAmount = $this
                    ->calculateTaxTotalForItems($calculableObjectTransfer->getItems());

                $totalTaxAmount += $this
                    ->calculateTaxTotalAmountForExpenses($concreteTimeSlot->getExpenses());

                $taxTotalTransfer = new TaxTotalTransfer();
                $taxTotalTransfer
                    ->setAmount((int)round($totalTaxAmount));

                $concreteTimeSlot
                    ->getTotals()
                    ->setTaxTotal($taxTotalTransfer);
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     *
     * @return int
     */
    protected function calculateTaxTotalAmountForExpenses(ArrayObject $expenses)
    {
        $totalTaxAmount = 0;
        foreach ($expenses as $expenseTransfer) {
            $expenseTransfer->requireSumTaxAmount();

            if($expenseTransfer->getIsNegative() === true){
                $totalTaxAmount -= $expenseTransfer->getSumTaxAmount() + $expenseTransfer->getTaxAmountAfterCancellation();
            } else {
                $totalTaxAmount += $expenseTransfer->getSumTaxAmount() - $expenseTransfer->getTaxAmountAfterCancellation();
            }
        }
        return $totalTaxAmount;
    }
}
