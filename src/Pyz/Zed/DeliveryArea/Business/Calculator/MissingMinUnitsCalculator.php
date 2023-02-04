<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 11.10.18
 * Time: 15:34
 */

namespace Pyz\Zed\DeliveryArea\Business\Calculator;


use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class MissingMinUnitsCalculator implements CalculatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this
                    ->calculateMissingMinUnitsForTimeSlot($concreteTimeSlot, $calculableObjectTransfer->getItems());
            }
        } else {
            $this
                ->calculateMissingMinUnits($calculableObjectTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer[]|\ArrayObject $items
     */
    protected function calculateMissingMinUnitsForTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer, ArrayObject $items): void
    {
        $concreteTimeSlotTransfer
            ->requireTotals();

        $minUnits = $concreteTimeSlotTransfer
            ->getMinUnits();

        foreach ($items as $item) {
            $minUnits -= $item->getQuantity();
        }

        $concreteTimeSlotTransfer
            ->getTotals()
            ->setMissingMinUnitsTotal(
                max(
                    0,
                    $minUnits
                )
            );
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    protected function calculateMissingMinUnits(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $minUnits = $calculableObjectTransfer
            ->getOriginalQuote()
            ->getMinUnits();

        foreach ($calculableObjectTransfer->getItems() as $item) {
            $minUnits -= $item->getQuantity();
        }

        $calculableObjectTransfer
            ->getTotals()
            ->setMissingMinUnitsTotal(
                max(
                    0,
                    $minUnits
                )
            );
    }
}
