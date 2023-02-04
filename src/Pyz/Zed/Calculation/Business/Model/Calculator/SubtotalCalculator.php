<?php
/**
 * Durst - project - SubtotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 16:32
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Calculation\Business\Model\Calculator\SubtotalCalculator as SprykerSubtotalCalculator;

class SubtotalCalculator extends SprykerSubtotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $calculableObjectTransfer
                ->requireTotals();

            $subtotal = $this
                ->calculateTotalItemSumAggregation($calculableObjectTransfer->getItems());

            $calculableObjectTransfer
                ->getTotals()
                ->setSubtotal(
                    $subtotal
                );

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->requireTotals();

                $subtotal -= $this
                    ->calculateTotalSubtotalWithRefundForTimeSlots($concreteTimeSlot);

                $concreteTimeSlot
                    ->getTotals()
                    ->setSubtotal($subtotal);
            }
        } else {
            $calculableObjectTransfer
                ->requireTotals();

            $subtotal = $this
                ->calculateTotalItemSumAggregation($calculableObjectTransfer->getItems());

            $subtotal -= $this
                ->calculateTotalSubtotalWithRefund($calculableObjectTransfer);

            $calculableObjectTransfer
                ->getTotals()
                ->setSubtotal($subtotal);
        }
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return int
     */
    protected function calculateTotalSubtotalWithRefund(CalculableObjectTransfer $calculableObjectTransfer) : int
    {
        $refundAmount = 0;
        foreach ($calculableObjectTransfer->getExpenses() as $expense) {
            if($expense->getType() === SalesConstants::REFUND_EXPENSE_TYPE){
                $refundAmount += $expense->getSumPrice();
            }
        }

        return $refundAmount;
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return int
     */
    protected function calculateTotalSubtotalWithRefundForTimeSlots(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : int
    {
        $refundAmount = 0;
        foreach ($concreteTimeSlotTransfer->getExpenses() as $expense) {
            if($expense->getType() === SalesConstants::REFUND_EXPENSE_TYPE){
                $refundAmount += $expense->getSumPrice();
            }
        }
        return $refundAmount;
    }
}

