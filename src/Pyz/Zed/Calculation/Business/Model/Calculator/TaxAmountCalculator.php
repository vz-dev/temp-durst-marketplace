<?php
/**
 * Durst - project - TaxAmountCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 11:07
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Tax\Business\Model\Calculator\TaxAmountCalculator as SprykerTaxAmountCalculator;

class TaxAmountCalculator extends SprykerTaxAmountCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $priceMode = $calculableObjectTransfer->getPriceMode();

            $haveExpenses = count($calculableObjectTransfer->getExpenses()) > 0;

            $this->calculateTaxSumAmountForItems($calculableObjectTransfer->getItems(), $priceMode, $haveExpenses);
            $this->calculateTaxSumAmountForProductOptions($calculableObjectTransfer->getItems(), $priceMode);

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculateTaxSumAmountForExpenses($concreteTimeSlot->getExpenses(), $priceMode);
            }

            $this->accruedTaxCalculator->resetRoundingErrorDelta();

            $this->calculateTaxUnitAmountForItems($calculableObjectTransfer->getItems(), $priceMode, $haveExpenses);
            $this->calculateTaxUnitAmountForProductOptions($calculableObjectTransfer->getItems(), $priceMode);

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculateTaxUnitAmountForExpenses($concreteTimeSlot->getExpenses(), $priceMode);
            }

            $this->accruedTaxCalculator->resetRoundingErrorDelta();
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }
}
