<?php
/**
 * Durst - project - InitialGrandTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 10:59
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\InitialGrandTotalCalculator as SprykerInitialGrandTotalCalculator;

class InitialGrandTotalCalculator extends SprykerInitialGrandTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $calculableObjectTransfer
                ->requireTotals();

            $grandTotal = 0;
            $grandTotal = $this
                ->calculateItemGrandTotal($calculableObjectTransfer, $grandTotal);

            $calculableObjectTransfer
                ->getTotals()
                ->setGrandTotal(
                    $grandTotal
                );

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $timeSlotGrandTotal = $this
                    ->calculateExpenseGrandTotalForTimeSlots($concreteTimeSlot->getExpenses(), $grandTotal);

                $concreteTimeSlot
                    ->getTotals()
                    ->setGrandTotal($timeSlotGrandTotal);
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @param int $grandTotal
     *
     * @return int
     */
    protected function calculateItemGrandTotal(CalculableObjectTransfer $calculableObjectTransfer, $grandTotal): int
    {
        foreach ($calculableObjectTransfer->getItems() as $itemTransfer) {
            $grandTotal += $itemTransfer->getSumSubtotalAggregation();
        }

        return $grandTotal;
    }

    /**
     * @param \ArrayObject $expenses
     * @param $grandTotal
     * @return int
     */
    protected function calculateExpenseGrandTotalForTimeSlots(
        ArrayObject $expenses,
        $grandTotal
    ): int
    {
        foreach ($expenses as $expenseTransfer) {
            $grandTotal += $expenseTransfer->getSumPrice();
        }

        return $grandTotal;
    }
}
