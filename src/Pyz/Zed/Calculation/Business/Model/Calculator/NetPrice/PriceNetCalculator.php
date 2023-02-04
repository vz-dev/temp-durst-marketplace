<?php


namespace Pyz\Zed\Calculation\Business\Model\Calculator\NetPrice;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\NetPrice\PriceNetCalculator as SprykerPriceNetCalculator;

class PriceNetCalculator extends SprykerPriceNetCalculator
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
