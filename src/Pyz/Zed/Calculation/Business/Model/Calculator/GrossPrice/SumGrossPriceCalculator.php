<?php


namespace Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\GrossPrice\SumGrossPriceCalculator as SprykerSumGrossPriceCalculator;

class SumGrossPriceCalculator extends SprykerSumGrossPriceCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $this->calculateItemGrossAmountForItems($calculableObjectTransfer->getItems());

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculateSumGrossPriceForExpenses($concreteTimeSlot->getExpenses());
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }
}
