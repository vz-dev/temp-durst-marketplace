<?php

namespace Pyz\Zed\DeliveryArea\Business\Writer;

use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\SpyConcreteTimeSlotEntityTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class ConcreteTimeSlotWriter
{
    const DATE_FORMAT = 'H:i:s';

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
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return SpyConcreteTimeSlotEntityTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer)
    {
        $concreteTimeSlotEntity = new SpyConcreteTimeSlot();
        $concreteTimeSlotEntity->fromArray($concreteTimeSlotTransfer->toArray());

        $concreteTimeSlotEntity->setStartTime(\DateTime::createFromFormat(self::DATE_FORMAT, $concreteTimeSlotTransfer->getStartTime()));
        $concreteTimeSlotEntity->setEndTime(\DateTime::createFromFormat(self::DATE_FORMAT, $concreteTimeSlotTransfer->getEndTime()));
        $concreteTimeSlotEntity->save();

        return $this->entityToTransfer($concreteTimeSlotEntity);


    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return SpyConcreteTimeSlotEntityTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function entityToTransfer(SpyConcreteTimeSlot $concreteTimeSlotEntity)
    {
        $transfer = new SpyConcreteTimeSlotEntityTransfer();
        $transfer->fromArray($concreteTimeSlotEntity->toArray(), true);
        $transfer->setStartTime($concreteTimeSlotEntity->getStartTime()->format(self::DATE_FORMAT));
        $transfer->setEndTime($concreteTimeSlotEntity->getEndTime()->format(self::DATE_FORMAT));

        return $transfer;
     }
}