<?php


namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\RemoveTotalsCalculator as SprykerRemoveTotalsCalculator;

class RemoveTotalsCalculator extends SprykerRemoveTotalsCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $totalsTransfer = $this
                    ->createTotalsTransfer();
                $totalsTransfer
                    ->setTaxTotal($this->createTaxTotalsTransfer());
                $totalsTransfer
                    ->setDiscountTotal(0);
                $totalsTransfer
                    ->setExpenseTotal(0);

                $concreteTimeSlot
                    ->setTotals($totalsTransfer);
            }
        }

        parent::recalculate($calculableObjectTransfer);
    }
}
