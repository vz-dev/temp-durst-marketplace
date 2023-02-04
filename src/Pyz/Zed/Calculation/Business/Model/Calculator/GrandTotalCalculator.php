<?php
/**
 * Durst - project - GrandTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 16:40
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException;
use Spryker\Zed\Calculation\Business\Model\Calculator\GrandTotalCalculator as SprykerGrandTotalCalculator;

class GrandTotalCalculator extends SprykerGrandTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     * @throws \Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $grandTotal = 0;
            $grandTotal = $this
                ->calculateItemGrandTotal($calculableObjectTransfer, $grandTotal);

            if (
                $grandTotal < 0 &&
                $calculableObjectTransfer->getOriginalOrder() !== null &&
                $calculableObjectTransfer->getOriginalOrder()->getIsExternal() !== true
            ) {
                throw new GrandTotalIsNegativeException(GrandTotalIsNegativeException::MESSAGE);
            }

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $timeSlotGrandTotal = $this
                    ->calculateExpenseGrandTotalForTimeSlots($concreteTimeSlot->getExpenses(), $grandTotal);

                if (
                    $timeSlotGrandTotal < 0 &&
                    $calculableObjectTransfer->getOriginalOrder() !== null &&
                    $calculableObjectTransfer->getOriginalOrder()->getIsExternal() !== true
                ) {
                    throw new GrandTotalIsNegativeException(GrandTotalIsNegativeException::MESSAGE);
                }

                $totalsTransfer = $concreteTimeSlot
                    ->getTotals();
                $totalsTransfer
                    ->setHash($this->generateTotalsHash($grandTotal));
                $totalsTransfer
                    ->setGrandTotal($timeSlotGrandTotal);

            }
        } else {
            $grandTotal = 0;
            $grandTotal = $this->calculateItemGrandTotal($calculableObjectTransfer, $grandTotal);

            if (
                $grandTotal < 0 &&
                $calculableObjectTransfer->getOriginalOrder() !== null &&
                $calculableObjectTransfer->getOriginalOrder()->getIsExternal() !== true
            ) {
                throw new GrandTotalIsNegativeException(GrandTotalIsNegativeException::MESSAGE);
            }

            $calculableObjectTransfer
                ->requireTotals();

            $timeSlotGrandTotal = $this
                ->calculateExpenseGrandTotal($calculableObjectTransfer, $grandTotal);

            if (
                $timeSlotGrandTotal < 0 &&
                $calculableObjectTransfer->getOriginalOrder() !== null &&
                $calculableObjectTransfer->getOriginalOrder()->getIsExternal() !== true
            ) {
                throw new GrandTotalIsNegativeException(GrandTotalIsNegativeException::MESSAGE);
            }

            $totalsTransfer = $calculableObjectTransfer
                ->getTotals();
            $totalsTransfer
                ->setHash($this->generateTotalsHash($grandTotal));
            $totalsTransfer
                ->setGrandTotal($timeSlotGrandTotal);
        }
    }

    /**
     * @param \ArrayObject $expenses
     * @param $grandTotal
     * @return mixed
     */
    protected function calculateExpenseGrandTotalForTimeSlots(ArrayObject $expenses, $grandTotal)
    {
        foreach ($expenses as $expenseTransfer) {
            if ($expenseTransfer->getIsNegative() === true) {
                $grandTotal -= $expenseTransfer->getSumPriceToPayAggregation() + $expenseTransfer->getCanceledAmount();
            } else {
                $grandTotal += $expenseTransfer->getSumPriceToPayAggregation() - $expenseTransfer->getCanceledAmount();
            }
        }
        return $grandTotal;
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @param int $grandTotal
     *
     * @return int
     */
    protected function calculateExpenseGrandTotal(CalculableObjectTransfer $calculableObjectTransfer, $grandTotal)
    {
        foreach ($calculableObjectTransfer->getExpenses() as $expenseTransfer) {

            if ($expenseTransfer->getIsNegative() === true) {
                $grandTotal -= $expenseTransfer->getSumPriceToPayAggregation() + $expenseTransfer->getCanceledAmount();
            } else {
                $grandTotal += $expenseTransfer->getSumPriceToPayAggregation() - $expenseTransfer->getCanceledAmount();
            }
        }
        return $grandTotal;
    }
}
