<?php
/**
 * Durst - project - TimeSlotSorter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 16:01
 */

namespace Pyz\Zed\DeliveryArea\Business\Finder;


use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;

class TimeSlotSorter
{
    /**
     * @param ConcreteTimeSlotTransfer[] $timeSlots
     * @return void
     */
    public static function sortTimeSlotsByStart(array &$timeSlots) : void
    {
        foreach($timeSlots as $branchId => &$timeSlotArray) {
            usort($timeSlotArray, [TimeSlotSorter::class, 'compareTimeSlotsByStart']);
        }
    }

    /**
     * @param array $timeSlots
     */
    public static function sortLimitedTimeSlots(array &$timeSlots) : void
    {
        usort($timeSlots, [TimeSlotSorter::class, 'compareTimeSlotsByStart']);
    }

    /**
     * @param array $branchTimeSlots
     * @return array
     */
    public static function mergeBranchTimeSlotArrays(array &$branchTimeSlots) : array
    {
        $sortedTimeSlotArray = [];
        self::merge($branchTimeSlots, $sortedTimeSlotArray);
        return $sortedTimeSlotArray;
    }

    /**
     * @param array $branchTimeSlots
     * @param array $sortedTimeSlotArray
     */
    protected static function merge(array &$branchTimeSlots, array &$sortedTimeSlotArray){
        if($branchTimeSlots == false){
            return;
        }

        foreach($branchTimeSlots as $branchId => &$timeSlotArray){
            if($timeSlotArray == false){
                unset($branchTimeSlots[$branchId]);
                continue;
            }

            $sortedTimeSlotArray[] = array_shift($timeSlotArray);
        }

        self::merge($branchTimeSlots, $sortedTimeSlotArray);
    }

    /**
     * @param ConcreteTimeSlotTransfer $a
     * @param ConcreteTimeSlotTransfer $b
     * @return int
     */
    public static function compareTimeSlotsByStart(ConcreteTimeSlotTransfer $a, ConcreteTimeSlotTransfer $b)
    {
        $aStartTime = \DateTime::createFromFormat($a->getTimeFormat(), $a->getStartTime());
        $bStartTime = \DateTime::createFromFormat($b->getTimeFormat(), $b->getStartTime());

        if($aStartTime < $bStartTime){
            return -1;
        }
        return 1;
    }
}