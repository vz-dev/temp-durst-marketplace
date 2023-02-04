<?php
/**
 * Durst - project - ConcreteTimeSlotCreator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.11.18
 * Time: 13:37
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;

use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Model\AssertionChecker;
use Pyz\Zed\DeliveryArea\Business\Model\TimeSlot;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

class ConcreteTimeSlotCreator implements ConcreteTimeSlotCreatorInterface
{
    protected const INTERVAL_SPEC = 'P1D';

    /**
     * @var \Pyz\Zed\DeliveryArea\DeliveryAreaConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Model\TimeSlot
     */
    protected $timeSlotModel;

    /**
     * @var \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface
     */
    protected $touchFacade;

    /**
     * @var \Pyz\Zed\Tour\Business\TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Model\AssertionChecker
     */
    protected $assertionChecker;

    /**
     * @var \DateInterval
     */
    protected $interval;

    /**
     * @var SoftwarePackageFacadeInterface
     */
    protected $softwarePackageFacade;

    /**
     * @var IntegraFacadeInterface
     */
    protected $integraFacade;

    /**
     * ConcreteTimeSlotCreator constructor.
     *
     * @param DeliveryAreaConfig $config
     * @param TimeSlot $timeSlotModel
     * @param TourFacadeInterface $tourFacade
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     * @param DeliveryAreaToTouchBridgeInterface $touchFacade
     * @param AssertionChecker $assertionChecker
     * @param SoftwarePackageFacadeInterface $softwarePackageFacade
     * @param IntegraFacadeInterface $integraFacade
     */
    public function __construct(
        DeliveryAreaConfig $config,
        TimeSlot $timeSlotModel,
        DeliveryAreaQueryContainerInterface $queryContainer,
        DeliveryAreaToTouchBridgeInterface $touchFacade,
        TourFacadeInterface $tourFacade,
        AssertionChecker $assertionChecker,
        SoftwarePackageFacadeInterface $softwarePackageFacade,
        IntegraFacadeInterface $integraFacade
    ) {
        $this->config = $config;
        $this->timeSlotModel = $timeSlotModel;
        $this->queryContainer = $queryContainer;
        $this->touchFacade = $touchFacade;
        $this->tourFacade = $tourFacade;
        $this->assertionChecker = $assertionChecker;
        $this->softwarePackageFacade = $softwarePackageFacade;
        $this->integraFacade = $integraFacade;
    }

    /**
     * @return void
     */
    public function createConcreteTimeSlots()
    {
        foreach ($this->timeSlotModel->getTimeSlotsForActiveBranches() as $timeSlotTransfer) {
            $this->createConcreteTimeSlotsForTimeSlotUntilLimit($timeSlotTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\TimeSlotTransfer $timeSlotTransfer
     *
     * @return void
     */
    protected function createConcreteTimeSlotsForTimeSlotUntilLimit(TimeSlotTransfer $timeSlotTransfer)
    {
        $dateToCheck = new DateTime('now');
        $dateToCheck->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
        $limit = new DateTime($this->config->getConcreteTimeSlotCreationLimit());

        while ($dateToCheck < $limit) {
            if ($this->checkDate($dateToCheck, $timeSlotTransfer) === true) {
                $this->createConcreteTimeSlotEntity(clone $dateToCheck, $timeSlotTransfer);
            }

            $dateToCheck->add($this->getInterval());
        }
    }

    /**
     * @param \DateTime $date
     * @param \Generated\Shared\Transfer\TimeSlotTransfer $timeSlotTransfer
     *
     * @return void
     */
    protected function createConcreteTimeSlotEntity(DateTime $date, TimeSlotTransfer $timeSlotTransfer)
    {
        $date->setTime(
            $this->getHourFromString($timeSlotTransfer->getStartTime()),
            $this->getMinuteFromString($timeSlotTransfer->getStartTime())
        );
        $start = clone $date;
        $start->setTimezone(new DateTimeZone('UTC'));


        $date->setTime(
            $this->getHourFromString($timeSlotTransfer->getEndTime()),
            $this->getMinuteFromString($timeSlotTransfer->getEndTime())
        );
        $end = clone $date;
        $end->setTimezone(new DateTimeZone('UTC'));

        $entity = $this
            ->getConcreteTimeSlotEntity($start, $end, $timeSlotTransfer->getIdTimeSlot());

        if (true !== $this->assertionChecker->isValid($entity)) {
            return;
        }

        if ($entity->isNew()) {

            $concreteTourTransfer = $this
                ->tourFacade
                ->createConcreteTourForConcreteTimeSlot(
                    $this->entityToTransfer($entity, $timeSlotTransfer->getFkBranch())
                );
            if ($concreteTourTransfer !== null) {
                $entity->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
            }

            $entity->save();

            if (
                $this->doesBranchUseIntegra($timeSlotTransfer) === true
                || $this->hasMerchantRetailPackage($timeSlotTransfer) === true
                || $entity->getFkConcreteTour() !== null
            ) {
                $this->insertActiveTouchConcreteTimeSlotRecord($entity->getIdConcreteTimeSlot());
            }
        }
    }

    /**
     * @param string $time
     *
     * @return int
     */
    protected function getHourFromString(string $time): int
    {
        $time = $this->stringToDateTime($time);
        return (int)$time->format('H');
    }

    /**
     * @param string $time
     *
     * @return int
     */
    protected function getMinuteFromString(string $time): int
    {
        $time = $this->stringToDateTime($time);
        return (int)$time->format('i');
    }

    /**
     * @param string $time
     *
     * @return \DateTime
     */
    protected function stringToDateTime(string $time): DateTime
    {
        return DateTime::createFromFormat($this->config->getTimeFormat(), $time);
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param int $fkTimeSlot
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot
     */
    protected function getConcreteTimeSlotEntity(DateTime $start, DateTime $end, int $fkTimeSlot): SpyConcreteTimeSlot
    {
        return $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByStartTime($start)
            ->filterByEndTime($end)
            ->filterByFkTimeSlot($fkTimeSlot)
            ->findOneOrCreate();
    }

    /**
     * @param \DateTime $dateToCheck
     * @param \Generated\Shared\Transfer\TimeSlotTransfer $timeSlotTransfer
     *
     * @return bool
     */
    protected function checkDate(DateTime $dateToCheck, TimeSlotTransfer $timeSlotTransfer): bool
    {
        $weekDay = $dateToCheck->format('l');
        $weekDayGetter = 'get' . $weekDay;
        if ($timeSlotTransfer->{$weekDayGetter}() !== true) {
            return false;
        }

        return true;
    }

    /**
     * @return \DateInterval
     */
    protected function createInterval(): DateInterval
    {
        return new DateInterval(static::INTERVAL_SPEC);
    }

    /**
     * @return \DateInterval
     */
    protected function getInterval(): DateInterval
    {
        if ($this->interval === null) {
            $this->interval = $this->createInterval();
        }

        return $this->interval;
    }

    /**
     * @param int $idItem
     *
     * @return bool
     */
    protected function insertActiveTouchConcreteTimeSlotRecord(int $idItem): bool
    {
        return $this
            ->touchFacade
            ->touchActive(DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT, $idItem);
    }

    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot $entity
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    protected function entityToTransfer(SpyConcreteTimeSlot $entity, int $idBranch): ConcreteTimeSlotTransfer
    {
        $transfer = new ConcreteTimeSlotTransfer();
        $transfer->fromArray($entity->toArray(), true);
        $transfer->setTimeFormat($this->config->getDateTimeFormat());
        $transfer->setIdBranch($idBranch);
        if ($entity->getStartTime() !== null) {
            $startTime = clone $entity->getStartTime();
            $startTime->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setStartTime($startTime->format($this->config->getDateTimeFormat()));
        }
        if ($entity->getEndTime() !== null) {
            $endTime = clone $entity->getEndTime();
            $endTime->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setEndTime($endTime->format($this->config->getDateTimeFormat()));
        }

        return $transfer;
    }

    /**
     * @param TimeSlotTransfer $timeSlotTransfer
     * @return bool
     */
    protected function hasMerchantRetailPackage(TimeSlotTransfer $timeSlotTransfer): bool
    {
        return $this->softwarePackageFacade->hasMerchantRetailPackage(
            $timeSlotTransfer->getBranch()->getFkMerchant()
        );
    }

    /**
     * @param TimeSlotTransfer $timeSlotTransfer
     * @return bool
     */
    protected function doesBranchUseIntegra(TimeSlotTransfer $timeSlotTransfer): bool
    {
        return $this->integraFacade->doesBranchUseIntegra(
            $timeSlotTransfer->getBranch()->getIdBranch()
        );
    }
}
