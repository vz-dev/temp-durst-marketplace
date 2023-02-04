<?php
/**
 * Durst - project - TaxTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 16:43
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\TaxTotalCalculator as SprykerTaxTotalCalculator;

class TaxTotalCalculator extends SprykerTaxTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $totalTaxAmount = $this->calculateTaxTotalForItems($calculableObjectTransfer->getItems());

        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $timeSlotTotalTaxAmount = $totalTaxAmount + $this
                        ->calculateTaxTotalAmountForExpenses($concreteTimeSlot->getExpenses());

                $taxTotalTransfer = new TaxTotalTransfer();
                $taxTotalTransfer
                    ->setAmount((int)round($timeSlotTotalTaxAmount));

                $concreteTimeSlot
                    ->getTotals()
                    ->setTaxTotal($taxTotalTransfer);
            }
        } else {
            $calculableObjectTransfer
                ->requireTotals();

            $timeSlotTotalTaxAmount = $totalTaxAmount + $this
                ->calculateTaxTotalAmountForExpenses($calculableObjectTransfer->getExpenses());

            $taxTotalTransfer = new TaxTotalTransfer();
            $taxTotalTransfer
                ->setAmount((int)round($timeSlotTotalTaxAmount));

            $calculableObjectTransfer
                ->getTotals()
                ->setTaxTotal($taxTotalTransfer);
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

            if($expenseTransfer->getIsNegative() === true){
                $totalTaxAmount -= $expenseTransfer->getSumTaxAmount();
            } else {
                $totalTaxAmount += $expenseTransfer->getSumTaxAmount();
            }
        }
        return $totalTaxAmount;
    }
}
