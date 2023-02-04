<?php
/**
 * Durst - project - MerchantPriceCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.06.18
 * Time: 10:18
 */

namespace Pyz\Zed\MerchantPrice\Business\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;

class MerchantPriceCalculator
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $grossSubtotal = $concreteTimeSlot
                    ->getTotals()
                    ->getGrandTotal();

                foreach ($concreteTimeSlot->getExpenses() as $expense) {
                    $grossSubtotal -= $expense->getSumPrice();
                }

                $concreteTimeSlot
                    ->getTotals()
                    ->setGrossSubtotal($grossSubtotal);
            }
        } else {
            $grossSubtotal = $calculableObjectTransfer
                ->getTotals()
                ->getGrandTotal();

            foreach ($calculableObjectTransfer->getExpenses() as $expenseTransfer) {
                $grossSubtotal -= $expenseTransfer->getSumPrice();
            }

            $calculableObjectTransfer
                ->getTotals()
                ->setGrossSubtotal($grossSubtotal);
        }
    }
}
