<?php


namespace Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\GrossPrice\PriceGrossCalculator as SprykerPriceGrossCalculator;

class PriceGrossCalculator extends SprykerPriceGrossCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $this->calculatePriceForItems($calculableObjectTransfer->getItems());

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculatePricesForExpenses($concreteTimeSlot->getExpenses());
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }
}
