<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 13:11
 */

namespace Pyz\Zed\Tour\Business\Model;

use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\AbstractTourExistsException;
use Pyz\Zed\Tour\Business\Exception\AbstractTourNotExistsException;
use Pyz\Zed\Tour\Business\Model\AbstractTourHydrator\AbstractTourHydratorInterface;
use Pyz\Zed\Tour\Business\Model\Saver\AbstractTimeSlotSaverInterface;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridgeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class AbstractTour implements AbstractTourInterface
{
    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var AbstractTourHydratorInterface[]
     */
    protected $hydrators;

    /**
     * @var AbstractTimeSlotSaverInterface
     */
    protected $abstractTimeSlotSaver;

    /**
     * @var TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * @var TourToStateMachineBridgeInterface
     */
    protected $stateMachineFacade;

    /**
     * AbstractTour constructor.
     * @param TourQueryContainerInterface $queryContainer
     * @param AbstractTourHydratorInterface[] $hydrators
     * @param AbstractTimeSlotSaverInterface $abstractTimeSlotSaver
     * @param TouchFacadeInterface $touchFacade
     * @param TourToStateMachineBridgeInterface $stateMachineFacade
     */
    public function __construct(
        TourQueryContainerInterface $queryContainer,
        array $hydrators,
        AbstractTimeSlotSaverInterface $abstractTimeSlotSaver,
        TouchFacadeInterface $touchFacade,
        TourToStateMachineBridgeInterface $stateMachineFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->hydrators = $hydrators;
        $this->abstractTimeSlotSaver = $abstractTimeSlotSaver;
        $this->touchFacade = $touchFacade;
        $this->stateMachineFacade = $stateMachineFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     * @throws AbstractTourNotExistsException
     */
    public function getAbstractTourById(int $idAbstractTour): AbstractTourTransfer
    {
        $abstractTourEntity = $this
            ->queryContainer
            ->queryAbstractTourById($idAbstractTour)
            ->findOne();

        if ($abstractTourEntity === null) {
            throw new AbstractTourNotExistsException(
                sprintf(AbstractTourNotExistsException::ID_NOT_EXISTS_MESSAGE, $idAbstractTour)
            );
        }

        return $this->entityToTransfer($abstractTourEntity);
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function save(AbstractTourTransfer $abstractTourTransfer): AbstractTourTransfer
    {
        $this->checkAssertions($abstractTourTransfer);

        $abstractTourEntity = $this->findEntityOrCreate($abstractTourTransfer);

        $abstractTourEntity->fromArray($abstractTourTransfer->toArray());
        $this->checkUnique($abstractTourEntity);

        if ($abstractTourEntity->isNew() || $abstractTourEntity->isModified()) {
            $abstractTourEntity->save();

            $abstractTourTransfer->setIdAbstractTour($abstractTourEntity->getIdAbstractTour());

            $this->touchActiveAllFutureRelatedConcreteTimeslots($abstractTourEntity->getIdAbstractTour());
        }

        $this
            ->abstractTimeSlotSaver
            ->saveAbstractTimeSlotsForAbstractTour($abstractTourTransfer);

        return $this->entityToTransfer($abstractTourEntity);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function activate(int $idAbstractTour): AbstractTourTransfer
    {
        $abstractTourTransfer = $this->getAbstractTourById($idAbstractTour);
        $abstractTourTransfer->setStatus(DstAbstractTourTableMap::COL_STATUS_ACTIVE);

        return $this->save($abstractTourTransfer);
    }

    /**
     * @param int $idAbstractTour
     *
     * @return bool
     */
    public function hasPreparationStartingSameTimeForAllTimeSlots(int $idAbstractTour): bool
    {
        $isFirst = true;
        $abstractTourTransfer = $this->getAbstractTourById($idAbstractTour);

        foreach ($abstractTourTransfer->getAbstractTimeSlots() as $abstractTimeSlotTransfer) {
            $startTime = strtotime($abstractTimeSlotTransfer->getStartTime());
            $prepTimeSeconds = $abstractTimeSlotTransfer->getPrepTime() * 60;
            $prepTimeStart = $startTime - $prepTimeSeconds;

            if ($isFirst === true) {
                $lastPrepTimeStart = $prepTimeStart;
                $isFirst = false;
            }

            if ($prepTimeStart !== $lastPrepTimeStart) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function deactivate(int $idAbstractTour): AbstractTourTransfer
    {
        $abstractTourTransfer = $this->getAbstractTourById($idAbstractTour);
        $abstractTourTransfer->setStatus(DstAbstractTourTableMap::COL_STATUS_DEACTIVATED);

        $this->removeConcreteToursWithoutOrders($idAbstractTour);

        return $this->save($abstractTourTransfer);
    }

    /**
     * @param int $idAbstractTour
     *
     * @return AbstractTourTransfer
     */
    public function delete(int $idAbstractTour): AbstractTourTransfer
    {
        $abstractTourTransfer = $this->getAbstractTourById($idAbstractTour);
        $abstractTourTransfer->setStatus(DstAbstractTourTableMap::COL_STATUS_DELETED);

        $this->removeConcreteToursWithoutOrders($idAbstractTour);

        return $this->save($abstractTourTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param $idBranch|int
     * @return abstractTourTransfer[]
     */
    public function getAbstractToursByFkBranch(int $idBranch): array
    {
        $abstractTourNotDeletedStatusMap = [
            DstAbstractTourTableMap::COL_STATUS_PLANNED,
            DstAbstractTourTableMap::COL_STATUS_ACTIVE,
            DstAbstractTourTableMap::COL_STATUS_DEACTIVATED,
        ];

        $abstractTourEntities = $this
            ->queryContainer
            ->queryAbstractTour()
            ->filterByFkBranch($idBranch)
            ->filterByStatus_In($abstractTourNotDeletedStatusMap)
            ->find();

        $abstractTourTransfers = [];
        foreach ($abstractTourEntities as $abstractTourEntity) {
            $abstractTourTransfers[] = $this->entityToTransfer($abstractTourEntity);
        }

        $sortByWeekdayStartEndName = function (AbstractTourTransfer $first, AbstractTourTransfer $second) {

            $firstStartTime = $this->getStartTimeAsTime($first);
            $secondStartTime = $this->getStartTimeAsTime($second);

            if ($firstStartTime < $secondStartTime) {
                return -1;
            }
            if ($secondStartTime < $firstStartTime) {
                return 1;
            }

            $firstEndTime = $this->getEndTimeAsTime($first);
            $secondEndTime = $this->getEndTimeAsTime($second);

            if ($firstEndTime < $secondEndTime) {
                return -1;
            }
            if ($secondEndTime < $firstEndTime) {
                return 1;
            }

            return strcasecmp($first->getname(), $second->getName());
        };

        usort($abstractTourTransfers, $sortByWeekdayStartEndName);

        return $abstractTourTransfers;
    }

    /**
     * @param DstAbstractTour $abstractTourEntity
     *
     * @return AbstractTourTransfer
     */
    public function entityToTransfer(DstAbstractTour $abstractTourEntity): AbstractTourTransfer
    {
        $abstractTourTransfer = new AbstractTourTransfer();
        $abstractTourTransfer->fromArray($abstractTourEntity->toArray(), true);

        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrateAbstractTour($abstractTourEntity, $abstractTourTransfer);
        }

        return $abstractTourTransfer;
    }

    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     *
     * @return false|int
     */
    protected function getStartTimeAsTime(AbstractTourTransfer $abstractTourTransfer)
    {
        return strtotime(sprintf(
            '%s %s',
            $abstractTourTransfer->getWeekday(),
            $abstractTourTransfer->getStartTime()
        ));
    }

    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     *
     * @return false|int
     */
    protected function getEndTimeAsTime(AbstractTourTransfer $abstractTourTransfer)
    {
        return strtotime(sprintf(
            '%s %s',
            $abstractTourTransfer->getWeekday(),
            $abstractTourTransfer->getEndTime()
        ));
    }

    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     *
     * @return DstAbstractTour
     */
    protected function findEntityOrCreate(AbstractTourTransfer $abstractTourTransfer): DstAbstractTour
    {
        if ($abstractTourTransfer->getIdAbstractTour() === null) {
            return new DstAbstractTour();
        }

        return $this
            ->queryContainer
            ->queryAbstractTourById($abstractTourTransfer->getIdAbstractTour())
            ->findOneOrCreate();
    }

    /**
     * @param DstAbstractTour $entity
     *
     * @return void
     *@throws AbstractTourExistsException
     *
     */
    protected function checkUnique(DstAbstractTour $entity)
    {
        if ($entity->isNew() && ($entity->getIdAbstractTour() !== null)) {
            throw new AbstractTourExistsException(
                sprintf(
                    AbstractTourExistsException::ID_EXISTS_MESSAGE,
                    $entity->getIdAbstractTour()
                )
            );
        }
    }

    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     *
     * @return void
     */
    protected function checkAssertions(AbstractTourTransfer $abstractTourTransfer)
    {
        $abstractTourTransfer->requireWeekday();
        $abstractTourTransfer->requireFkBranch();
        $abstractTourTransfer->requireFkVehicleType();
    }

    /**
     * @param int $idAbstractTour
     *
     * @return void
     * @throws AbstractTourNotExistsException
     *
     */
    protected function removeConcreteToursWithoutOrders(int $idAbstractTour)
    {
        $abstractTourEntity = $this
            ->queryContainer
            ->queryAbstractTourById($idAbstractTour)
            ->findOne();

        if ($abstractTourEntity === null) {
            throw new AbstractTourNotExistsException(
                sprintf(AbstractTourNotExistsException::ID_NOT_EXISTS_MESSAGE, $idAbstractTour)
            );
        }

        foreach ($abstractTourEntity->getDstConcreteTours() as $concreteTour) {
            $removeConcreteTour = false;
            $timeslotDetachedStats = [];

            foreach ($concreteTour->getSpyConcreteTimeSlots() as $timeSlot) {
                if ($timeSlot->getSpySalesOrders()->count() == 0) {
                    $timeSlot->setFkConcreteTour(null);
                    $timeSlot->save();

                    $timeslotDetachedStats[] = true;

                    continue;
                }

                $timeslotDetachedStats[] = false;
            }

            if (in_array(false, $timeslotDetachedStats, TRUE) !== true) {
                $removeConcreteTour = true;
            }

            if ($removeConcreteTour || $concreteTour->getSpyConcreteTimeSlots()->count() == 0) {
                if($concreteTour->getFkStateMachineItemState() !== null){
                    $this
                        ->stateMachineFacade
                        ->triggerEvent(
                            TourConstants::TOUR_STATE_EVENT_DELETE,
                            $this->createStateMachineItemTransfer($concreteTour)
                        );
                }
            }
        }
    }

    /**
     * @param DstConcreteTour $concreteTour
     * @return StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(DstConcreteTour $concreteTour): StateMachineItemTransfer
    {
        return (new StateMachineItemTransfer())
            ->setIdentifier($concreteTour->getIdConcreteTour())
            ->setIdItemState($concreteTour->getFkStateMachineItemState());
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return bool
     */
    protected function touchByIdConcreteTimeSlot(int $idConcreteTimeSlot): bool
    {
        return $this
            ->touchFacade
            ->touchDeleted(
                DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT,
                $idConcreteTimeSlot
            );
    }

    /**
     * @param int $abstractTourId
     *
     * @return void
     */
    protected function touchActiveAllFutureRelatedConcreteTimeslots(int $abstractTourId) : void
    {
        $futureTimeSlots = $this
            ->queryContainer
            ->queryFutureConcreteTimeSlotsByAbstractTourId($abstractTourId)
            ->find();

        foreach ($futureTimeSlots as $concreteTimeSlot)
        {
            $idConcreteTimeSlot = $concreteTimeSlot->getIdConcreteTimeSlot();
            $this->touchFacade
                ->touchActive(
                    DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT,
                    $idConcreteTimeSlot
                );
        }
    }
}
