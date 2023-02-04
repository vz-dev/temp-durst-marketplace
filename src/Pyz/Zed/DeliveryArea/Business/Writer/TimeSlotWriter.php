<?php

namespace Pyz\Zed\DeliveryArea\Business\Writer;

use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class TimeSlotWriter
{
    //TODO why are there two classes as branch model? Aren't they redundant?

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;


    /**
     * TimeSlotWriter constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     */
    public function __construct(DeliveryAreaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return bool
     */
    public function timeSlotsAreImported()
    {
        return $this->queryContainer->queryTimeSlot()->count() > 0;
    }

    /**
     * @param TimeSlotTransfer $timeSlotTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createTimeSlot(TimeSlotTransfer $timeSlotTransfer)
    {
        $timeSlotEntity = new SpyTimeSlot();
        $timeSlotEntity->fromArray($timeSlotTransfer->toArray());
        $timeSlotEntity->save();
    }

}