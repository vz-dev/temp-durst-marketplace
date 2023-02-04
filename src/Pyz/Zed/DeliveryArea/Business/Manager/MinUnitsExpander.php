<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 15.10.18
 * Time: 13:53
 */

namespace Pyz\Zed\DeliveryArea\Business\Manager;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Pyz\Zed\DeliveryArea\Business\Model\TimeSlot;

class MinUnitsExpander
{
    /**
     * @var TimeSlot
     */
    protected $timeslotModel;

    /**
     * MinUnitsExpander constructor.
     *
     * @param TimeSlot $timeslotModel
     */
    public function __construct(TimeSlot $timeslotModel)
    {
        $this->timeslotModel = $timeslotModel;
    }

    /**
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartChangeTransfer
     */
    public function expandItemsByMinUnits(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        $cartChangeTransfer
            ->requireQuote();

        $quote = $cartChangeTransfer->getQuote();

        if($quote->getUseFlexibleTimeSlots() === true)
        {
            $quote->setMinUnits(0);

            return $cartChangeTransfer;
        }

        $cartChangeTransfer
            ->getQuote()
            ->requireConcreteTimeSlots();

        $quote->setMinUnits($this->getMinUnits($quote->getConcreteTimeSlots()->offsetGet(0)));

        return $cartChangeTransfer;
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return int
     */
    protected function getMinUnits(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): int
    {
        if ($concreteTimeSlotTransfer->getMinUnits() !== null) {
            return $concreteTimeSlotTransfer->getMinUnits();
        }

        $timeSlot = $this
            ->timeslotModel
            ->getTimeSlotById($concreteTimeSlotTransfer->getFkTimeSlot());

        return $timeSlot->getMinUnits();
    }
}
