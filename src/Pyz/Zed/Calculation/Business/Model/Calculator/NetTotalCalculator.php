<?php
/**
 * Durst - project - NetTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 22:22
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\NetTotalCalculator as SprykerNetTotalCalculator;

class NetTotalCalculator extends SprykerNetTotalCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot->requireTotals();

                $totalsTransfer = $concreteTimeSlot
                    ->getTotals();

                $totalsTransfer->setNetTotal(
                    $totalsTransfer->getGrandTotal() - $totalsTransfer->getTaxTotal()->getAmount()
                );
            }
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }
}
