<?php
/**
 * Durst - project - MissingMinValueCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.05.18
 * Time: 16:17
 */

namespace Pyz\Zed\DeliveryArea\Business\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class MissingMinValueCalculator implements CalculatorInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this
                    ->calculateMissingMinValueForTimeSlot($concreteTimeSlot);
            }
        } else {
            $this
                ->calculateMissingMinValue($calculableObjectTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlot
     */
    protected function calculateMissingMinValueForTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlot) : void
    {
        $concreteTimeSlot
            ->requireTotals();

        $concreteTimeSlot
            ->getTotals()
            ->requireGrossSubtotal();

        $missingAmount = $concreteTimeSlot->getMinValue() - $concreteTimeSlot->getTotals()->getGrossSubtotal();

        $concreteTimeSlot
            ->getTotals()
            ->setMissingMinAmountTotal(
                max(
                    0,
                    $missingAmount
                )
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    protected function calculateMissingMinValue(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $calculableObjectTransfer
            ->requireTotals();

        $calculableObjectTransfer
            ->getTotals()
            ->requireGrossSubtotal();

        $missingAmount = $calculableObjectTransfer->getMinValue() - $calculableObjectTransfer->getTotals()->getGrossSubtotal();

        $calculableObjectTransfer
            ->getTotals()
            ->setMissingMinAmountTotal(
                max(
                    0,
                    $missingAmount
                )
            );
    }
}
