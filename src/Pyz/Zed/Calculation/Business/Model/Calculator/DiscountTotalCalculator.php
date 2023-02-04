<?php
/**
 * Durst - project - DiscountTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 22:10
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;


use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\DiscountTotalCalculator as SprykerDiscountTotalCalculator;

class DiscountTotalCalculator extends SprykerDiscountTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $totalDiscountAmount = $this->calculateItemTotalDiscountAmount($calculableObjectTransfer->getItems());

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $timeSlotDiscountAmount = $totalDiscountAmount + $this->calculateExpenseTotalDiscountAmountForTimeSlot($concreteTimeSlot->getExpenses());

                $concreteTimeSlot->getTotals()->setDiscountTotal($timeSlotDiscountAmount);
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }

    /**
     * @param \ArrayObject $expenses
     * @return int
     */
    protected function calculateExpenseTotalDiscountAmountForTimeSlot(ArrayObject $expenses)
    {
        $totalDiscountAmount = 0;
        foreach ($expenses as $expenseTransfer) {
            $totalDiscountAmount += $expenseTransfer->getSumDiscountAmountAggregation();
        }
        return $totalDiscountAmount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return int
     */
    protected function calculateItemTotalDiscountAmount(ArrayObject $items)
    {
        $totalDiscountAmount = 0;
        foreach ($items as $itemTransfer) {
            $totalDiscountAmount += $itemTransfer->getSumDiscountAmountFullAggregation();
        }
        return $totalDiscountAmount;
    }
}
