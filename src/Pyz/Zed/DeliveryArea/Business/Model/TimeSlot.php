<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:51
 */

namespace Pyz\Zed\DeliveryArea\Business\Model;

use DateTime;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Propel\Runtime\Collection\ObjectCollection;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotInvalidMaxCustomersValueException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotMalformedTimeStringException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotQueryInvalidWeekdayException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartOrEndNotSetException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartTimeBiggerEqualThanEndTimeException;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class TimeSlot
{
    /**
     * @var \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /** @var \Pyz\Zed\DeliveryArea\DeliveryAreaConfig */
    protected $config;

    /**
     * @var \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface[]
     */
    protected $concreteTimeSlotSavePlugins;

    /**
     * @var \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface[]
     */
    protected $concreteTimeSlotDeletePlugins;

    /**
     * TimeSlot constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\DeliveryArea\DeliveryAreaConfig $config
     * @param \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface[] $concreteTimeSlotSavePlugins
     * @param \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface[] $concreteTimeSlotDeletePlugins
     */
    public function __construct(
        DeliveryAreaQueryContainerInterface $queryContainer,
        DeliveryAreaConfig $config,
        array $concreteTimeSlotSavePlugins,
        array $concreteTimeSlotDeletePlugins
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->concreteTimeSlotSavePlugins = $concreteTimeSlotSavePlugins;
        $this->concreteTimeSlotDeletePlugins = $concreteTimeSlotDeletePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\TimeSlotTransfer $timeSlotTransfer
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotMalformedTimeStringException if the string defining a time doesn't
     * match the format @see \DateTime::ATOM
     * be found in the database
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartOrEndNotSetException if start OR end time are not set in the
     * transfer object. Note: This won't be thrown if both times aren't set
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartTimeBiggerEqualThanEndTimeException if the start time is
     * later than the end time
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer
     */
    public function save(TimeSlotTransfer $timeSlotTransfer)
    {
        //TODO make two different methods one for adding, one for updating
        if ($timeSlotTransfer->getIdTimeSlot() === null) {
            $timeSlotEntity = new SpyTimeSlot();
        } else {
            $timeSlotEntity = $this->getEntityTimeSlotById($timeSlotTransfer->getIdTimeSlot());
        }

        $timeSlotEntity->setIsActive($timeSlotTransfer->getIsActive());
        if ($timeSlotTransfer->getDeliveryCosts() !== null) {
            $timeSlotEntity->setDeliveryCosts($timeSlotTransfer->getDeliveryCosts());
        }
        $timeSlotEntity->setFkBranch($timeSlotTransfer->getFkBranch());
        $timeSlotEntity->setFkDeliveryArea($timeSlotTransfer->getFkDeliveryArea());
        if ($timeSlotTransfer->getMaxCustomers() !== null) {
            if ($timeSlotTransfer->getMaxCustomers() <= 0) {
                throw new TimeSlotInvalidMaxCustomersValueException();
            }

            $timeSlotEntity->setMaxCustomers($timeSlotTransfer->getMaxCustomers());
        }
        if ($timeSlotTransfer->getMaxProducts() !== null) {
            $timeSlotEntity->setMaxProducts($timeSlotTransfer->getMaxProducts());
        }
        if ($timeSlotTransfer->getMinValueFirst() !== null) {
            $timeSlotEntity->setMinValueFirst($timeSlotTransfer->getMinValueFirst());
        }
        if ($timeSlotTransfer->getMinValueFollowing() !== null) {
            $timeSlotEntity->setMinValueFollowing($timeSlotTransfer->getMinValueFollowing());
        }
        if ($timeSlotTransfer->getMinUnits() !== null) {
            $timeSlotEntity->setMinUnits($timeSlotTransfer->getMinUnits());
        }
        if ($timeSlotTransfer->getPrepTime() !== null) {
            $timeSlotEntity->setPrepTime($timeSlotTransfer->getPrepTime());
        }

        // throw exception if only start OR end time is set
        // to clarify this won't throw an exception if both values are null
        if ($timeSlotTransfer->getStartTime() !== null || $timeSlotTransfer->getEndTime() !== null) {
            if ($timeSlotTransfer->getStartTime() === null || $timeSlotTransfer->getEndTime() === null) {
                throw new TimeSlotStartOrEndNotSetException();
            }

            // TODO use time format from config file instead of DateTime constant
            $start = DateTime::createFromFormat(DateTime::ATOM, $timeSlotTransfer->getStartTime());
            $end = DateTime::createFromFormat(DateTime::ATOM, $timeSlotTransfer->getEndTime());

            if ($start === null || $end === null) {
                throw new TimeSlotMalformedTimeStringException();
            }

            if ($start > $end) {
                throw new TimeSlotStartTimeBiggerEqualThanEndTimeException();
            }

            $timeSlotEntity->setStartTime($start);
            $timeSlotEntity->setEndTime($end);
        }

        if ($timeSlotTransfer->getStartTime() !== null) {
            $timeSlotEntity->setStartTime($timeSlotTransfer->getStartTime());
        }
        if ($timeSlotTransfer->getEndTime() !== null) {
            $timeSlotEntity->setEndTime($timeSlotTransfer->getEndTime());
        }
        if ($timeSlotTransfer->getMonday() === null) {
            $timeSlotEntity->setMonday(false);
        } else {
            $timeSlotEntity->setMonday($timeSlotTransfer->getMonday());
        }
        if ($timeSlotTransfer->getTuesday() === null) {
            $timeSlotEntity->setTuesday(false);
        } else {
            $timeSlotEntity->setTuesday($timeSlotTransfer->getTuesday());
        }
        if ($timeSlotTransfer->getWednesday() === null) {
            $timeSlotEntity->setWednesday(false);
        } else {
            $timeSlotEntity->setWednesday($timeSlotTransfer->getWednesday());
        }
        if ($timeSlotTransfer->getThursday() === null) {
            $timeSlotEntity->setThursday(false);
        } else {
            $timeSlotEntity->setThursday($timeSlotTransfer->getThursday());
        }
        if ($timeSlotTransfer->getFriday() === null) {
            $timeSlotEntity->setFriday(false);
        } else {
            $timeSlotEntity->setFriday($timeSlotTransfer->getFriday());
        }
        if ($timeSlotTransfer->getSaturday() === null) {
            $timeSlotEntity->setSaturday(false);
        } else {
            $timeSlotEntity->setSaturday($timeSlotTransfer->getSaturday());
        }

        if ($timeSlotTransfer->getIntegraTourNo() !== null) {
            $timeSlotEntity->setIntegraTourNo($timeSlotTransfer->getIntegraTourNo());
        }

        if ($timeSlotTransfer->getIntegraDeliveryWindowNo() !== null) {
            $timeSlotEntity->setIntegraDeliveryWindowNo($timeSlotTransfer->getIntegraDeliveryWindowNo());
        }

        if($timeSlotEntity->isModified()){
            if($timeSlotEntity->getIdTimeSlot()){
                $this->removeOldConcreteTimeSlots($timeSlotEntity->getIdTimeSlot());
            }
        }

        $timeSlotEntity->save();

        if ($timeSlotTransfer->getIsActive() === true) {
            $this->activeTouchConcreteTimeSlots($timeSlotEntity->getIdTimeSlot());
        } else {
            $this->deleteTouchConcreteTimeSlots($timeSlotEntity->getIdTimeSlot());
        }

        $timeSlotTransfer = $this->entityToTransfer($timeSlotEntity);

        return $timeSlotTransfer;
    }

    /**
     * @param int $idBranch
     *
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranch(int $idBranch)
    {
        $timeSlotEntities = $this
            ->queryContainer
            ->queryTimeSlot()
            ->filterByFkBranch($idBranch)
            ->find();

        $timeSlotTransfers = [];
        foreach ($timeSlotEntities as $timeSlotEntity) {
            $timeSlotTransfers[] = $this->entityToTransfer($timeSlotEntity);
        }

        return $timeSlotTransfers;
    }

    /**
     * @param int $idBranch
     * @param string $weekday
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotQueryInvalidWeekdayException
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchAndWeekday(int $idBranch, string $weekday): array
    {
        if (!in_array($weekday, DstAbstractTourTableMap::getValueSet(DstAbstractTourTableMap::COL_WEEKDAY))) {
            if (!in_array($weekday, DstAbstractTourTableMap::getValueSet(DstAbstractTourTableMap::COL_WEEKDAY))) {
                throw new TimeSlotQueryInvalidWeekdayException();
            }
        }

        $timeSlotEntities = $this
            ->queryContainer
            ->queryActiveTimeSlotByIdBranchAndWeekday($idBranch, $weekday)
            ->find();

        $timeSlotTransfers = [];
        foreach ($timeSlotEntities as $timeSlotEntity) {
            $timeSlotTransfers[] = $this->entityToTransfer($timeSlotEntity);
        }

        return $timeSlotTransfers;
    }

    /**
     * @param int $idBranch
     * @param string $weekday
     * @param int $idAbstractTour
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotQueryInvalidWeekdayException
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchWeekdayAndAbstractTour(int $idBranch, string $weekday, int $idAbstractTour): array
    {
        if (!in_array($weekday, DstAbstractTourTableMap::getValueSet(DstAbstractTourTableMap::COL_WEEKDAY))) {
            if (!in_array($weekday, DstAbstractTourTableMap::getValueSet(DstAbstractTourTableMap::COL_WEEKDAY))) {
                throw new TimeSlotQueryInvalidWeekdayException();
            }
        }

        $timeSlotEntities = $this
            ->queryContainer
            ->queryActiveTimeSlotByIdBranchWeekdayAndAbstractTour($idBranch, $weekday, $idAbstractTour)
            ->find();

        $timeSlotTransfers = [];
        foreach ($timeSlotEntities as $timeSlotEntity) {
            $timeSlotTransfers[] = $this->entityToTransfer($timeSlotEntity);
        }

        return $timeSlotTransfers;
    }

    /**
     * @param int $idBranch
     * @param string $zipCode
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchAndZipCode(int $idBranch, string $zipCode): array
    {
        $timeSlotEntities = $this
            ->queryContainer
            ->queryTimeSlotByIdBranch($idBranch)
            ->useSpyDeliveryAreaQuery()
                ->filterByZipCode($zipCode)
            ->endUse()
            ->filterByIsActive(true)
            ->find();

        $timeSlotTransfers = [];
        foreach ($timeSlotEntities as $timeSlotEntity) {
            $timeSlotTransfers[] = $this->entityToTransfer($timeSlotEntity);
        }

        return $timeSlotTransfers;
    }

    /**
     * @param int $idTimeSlot
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException if there is no time slot with the given id
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot
     */
    public function getEntityTimeSlotById(int $idTimeSlot)
    {
        $entity = $this
            ->queryContainer
            ->queryTimeSlotById($idTimeSlot)
            ->findOne();

        if ($entity === null) {
            throw new TimeSlotNotFoundException();
        }

        return $entity;
    }

    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot $entity
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer
     */
    protected function entityToTransfer(SpyTimeSlot $entity)
    {
        $transfer = new TimeSlotTransfer();
        $transfer->fromArray($entity->toArray(), true);
        if ($entity->getStartTime() !== null) {
            $transfer->setStartTime($entity->getStartTime()->format($this->config->getTimeFormat()));
        }
        if ($entity->getEndTime() !== null) {
            $transfer->setEndTime($entity->getEndTime()->format($this->config->getTimeFormat()));
        }

        if ($entity->getCachedSpyBranch() !== null) {
            $branchTransfer = new BranchTransfer();
            $branchTransfer->fromArray($entity->getSpyBranch()->toArray(), true);

            $transfer->setBranch($branchTransfer);
        }

        return $transfer;
    }

    /**
     * @param int $idTimeSlot
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException
     *
     * @return void
     */
    public function removeTimeSlot(int $idTimeSlot)
    {
        $timeSlotEntity = $this
            ->queryContainer
            ->queryTimeSlotById($idTimeSlot)
            ->findOne();

        if ($timeSlotEntity === null) {
            throw new TimeSlotNotFoundException();
        }

        $this->deleteTouchConcreteTimeSlots($timeSlotEntity->getIdTimeSlot());

        $timeSlotEntity->setStatus(SpyTimeSlotTableMap::COL_STATUS_DELETED);
        $timeSlotEntity->save();

        $this->removeOldConcreteTimeSlots($timeSlotEntity->getIdTimeSlot());
    }

    /**
     * @param int $idTimeSlot
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException if the given id cannot be found in the
     * database
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer
     */
    public function getTimeSlotById($idTimeSlot)
    {
        $timeSlotEntity = $this
            ->queryContainer
            ->queryTimeSlot()
            ->filterByIdTimeSlot($idTimeSlot)
            ->findOne();

        if ($timeSlotEntity === null) {
            throw new TimeSlotNotFoundException(sprintf(TimeSlotNotFoundException::NOT_FOUND, $idTimeSlot));
        }

        return $this->entityToTransfer($timeSlotEntity);
    }

    /**
     * @param int $idBranch
     * @param int $idDeliveryArea
     *
     * @return \Generated\Shared\Transfer\TimeSlotTransfer[]
     */
    public function getTimeSlotByIdBranchAndIdDeliverArea($idBranch, $idDeliveryArea)
    {
        $timeSlotEntities = $this
            ->queryContainer
            ->queryTimeSlotByIdBranchAndIdDeliveryArea($idBranch, $idDeliveryArea)
            ->find();

        $timeSlotTransfers = [];
        foreach ($timeSlotEntities as $timeSlotEntity) {
            $timeSlotTransfers[] = $this->entityToTransfer($timeSlotEntity);
        }

        return $timeSlotTransfers;
    }

    /**
     * @param int $idTimeSlot
     *
     * @return void
     */
    protected function deleteTouchConcreteTimeSlots(int $idTimeSlot)
    {
        $concreteTimeSlots = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByFkTimeSlot($idTimeSlot)
            ->find();

        if ($concreteTimeSlots->count() > 0) {
            $this->runConcreteTimeSlotDeletePlugins($concreteTimeSlots);
        }
    }

    /**
     * @param int $idTimeSlot
     *
     * @return void
     */
    protected function activeTouchConcreteTimeSlots(int $idTimeSlot)
    {
        $concreteTimeSlots = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByFkTimeSlot($idTimeSlot)
            ->find();

        if ($concreteTimeSlots->count() > 0) {
            $this->runConcreteTimeSlotSavePlugins($concreteTimeSlots);
        }
    }

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     *
     * @return void
     */
    protected function runConcreteTimeSlotSavePlugins(ObjectCollection $concreteTimeSlots)
    {
        foreach ($this->concreteTimeSlotSavePlugins as $savePlugin) {
            $savePlugin->bulkSave($concreteTimeSlots);
        }
    }

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     *
     * @return void
     */
    protected function runConcreteTimeSlotDeletePlugins(ObjectCollection $concreteTimeSlots)
    {
        foreach ($this->concreteTimeSlotDeletePlugins as $deletePlugin) {
            $deletePlugin->bulkDelete($concreteTimeSlots);
        }
    }

    /**
     * @return \Generated\Shared\Transfer\TimeSlotTransfer[]
     */
    public function getTimeSlotsForActiveBranches(): array
    {
        $entities = $this
            ->queryContainer
            ->queryTimeSlotsForActiveBranches();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param int $idTimeSlot
     */
    protected function removeOldConcreteTimeSlots(int $idTimeSlot)
    {
        $concreteTimeSlots = $this
            ->queryContainer
            ->queryConcreteTimeSlotsWithoutOrdersByAbstractTimeSlotId($idTimeSlot)
            ->find();

        if ($concreteTimeSlots->count() > 0) {
            $concreteTimeSlots->delete();
            $this->runConcreteTimeSlotDeletePlugins($concreteTimeSlots);
        }
    }
}
