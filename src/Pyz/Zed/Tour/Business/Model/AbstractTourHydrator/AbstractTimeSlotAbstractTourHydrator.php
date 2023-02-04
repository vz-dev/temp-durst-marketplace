<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 14:14
 */

namespace Pyz\Zed\Tour\Business\Model\AbstractTourHydrator;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Orm\Zed\Tour\Persistence\DstAbstractTour;

class AbstractTimeSlotAbstractTourHydrator implements AbstractTourHydratorInterface
{
    public const START_END_TIME_FORMAT = 'H:i';

    /**
     * {@inheritdoc}
     *
     * @param DstAbstractTour $abstractTourEntity
     * @param AbstractTourTransfer $abstractTourTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function hydrateAbstractTour(
        DstAbstractTour $abstractTourEntity,
        AbstractTourTransfer $abstractTourTransfer
    )
    {
        $abstractTimeSlotIds = [];

        foreach ($abstractTourEntity->getDstAbstractTourToAbstractTimeSlots() as $abstractTourToAbstractTimeSlot) {
            $abstractTimeSlotEntity = $abstractTourToAbstractTimeSlot->getSpyTimeSlot();
            $abstractTourTransfer->addAbstractTimeSlots($this->entityToTransfer($abstractTimeSlotEntity));
            $abstractTimeSlotIds[] = $abstractTimeSlotEntity->getIdTimeSlot();
        }

        $abstractTourTransfer->setAbstractTimeSlotIds($abstractTimeSlotIds);

        $this->hydrateAbstractTourByStartAndEndTime($abstractTourTransfer);
    }

    /**
     * @param SpyTimeSlot $entity
     * @return TimeSlotTransfer
     * @return void
     */
    protected function entityToTransfer(SpyTimeSlot $entity) : TimeSlotTransfer
    {
        return (new TimeSlotTransfer())
            ->fromArray($entity->toArray(), true);
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     */
    protected function hydrateAbstractTourByStartAndEndTime(AbstractTourTransfer $abstractTourTransfer){

        $timesSet = false;

        foreach ($abstractTourTransfer->getAbstractTimeSlots() as $abstractTimeSlotTransfer) {

            $startTime = strtotime($abstractTimeSlotTransfer->getStartTime());
            $endTime = strtotime($abstractTimeSlotTransfer->getEndTime());

            $prepTimeSeconds = $abstractTimeSlotTransfer->getPrepTime() * 60;
            $prepTimeStart = $startTime - $prepTimeSeconds;


            if ($timesSet === false){
                $minStartTime = $startTime;
                $maxEndTime = $endTime;
                $lastPrepTimeStart = $prepTimeStart;
                $timesSet = true;
            }

            if ($startTime < $minStartTime){
                $minStartTime = $startTime;
            }
            if ($endTime > $maxEndTime){
                $maxEndTime = $endTime;
            }
            if ($prepTimeStart > $lastPrepTimeStart){
                $lastPrepTimeStart = $prepTimeStart;
            }
        }

        if ($timesSet){
            $abstractTourTransfer->setStartTime(date(self::START_END_TIME_FORMAT, $minStartTime));
            $abstractTourTransfer->setEndTime(date(self::START_END_TIME_FORMAT, $maxEndTime));

            $prepTimeMinutes = ($minStartTime - $lastPrepTimeStart) / 60;

            $abstractTourTransfer->setPrepTimeBufferMinutesBeforeStart($prepTimeMinutes);
        }
    }

}
