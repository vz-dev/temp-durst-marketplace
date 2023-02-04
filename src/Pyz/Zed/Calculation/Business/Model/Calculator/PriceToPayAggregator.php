<?php
/**
 * Durst - project - PriceToPayAggregator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 22:05
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Aggregator\PriceToPayAggregator as SprykerPriceToPayAggregator;

class PriceToPayAggregator extends SprykerPriceToPayAggregator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $this->calculatePriceToPayAggregationForItems($calculableObjectTransfer->getItems(), $calculableObjectTransfer->getPriceMode());

            if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
                foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                    $this->calculatePriceToPayAggregationForExpenses($concreteTimeSlot->getExpenses(), $calculableObjectTransfer->getPriceMode());
                }
            } else {
                $this->calculatePriceToPayAggregationForExpenses($calculableObjectTransfer->getExpenses(), $calculableObjectTransfer->getPriceMode());
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }
}
