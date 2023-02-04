<?php
/**
 * Durst - project - ConcreteTimeSlotRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 16.11.20
 * Time: 09:42
 */

namespace Pyz\Zed\Integra\Business\Model\TimeSlot;

use DateTime;
use DateTimeZone;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class ConcreteTimeSlotRepository implements ConcreteTimeSlotRepositoryInterface
{
    protected const DEFAULT_START_TIME_TIME_SLOT = '08:00:00';
    protected const DEFAULT_END_TIME_TIME_SLOT = '16:00:00';

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array
     */
    protected $concreteTimeSlots = [];

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var array
     */
    protected $integraAbstractTour = [];

    /**
     * ConcreteTimeSlotRepository constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
        $this->resetCounter();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zipCode
     * @param int $idBranch
     * @param string $start
     * @param string $end
     *
     * @return int
     */
    public function getTimeSlotId(
        string $zipCode,
        int $idBranch,
        string $start,
        string $end
    ): int {
        $startEnd = $start . $end;
        if (array_key_exists($zipCode, $this->concreteTimeSlots) !== true ||
            array_key_exists($startEnd, $this->concreteTimeSlots[$zipCode]) !== true) {
            $this->concreteTimeSlots[$zipCode][$startEnd] = $this->loadTimeSlot($zipCode, $idBranch, $start, $end);
        }

        return $this
            ->concreteTimeSlots[$zipCode][$startEnd];
    }

    /**
     * @return void
     */
    public function resetCounter(): void
    {
        $this->counter = 0;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @param string $zipCode
     * @param int $idBranch
     * @param string $start
     * @param string $end
     *
     * @return int
     */
    protected function loadTimeSlot(
        string $zipCode,
        int $idBranch,
        string $start,
        string $end
    ): int {

        $startTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $start)->setTimezone(new DateTimeZone('UTC'));
        $endTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $end)->setTimezone(new DateTimeZone('UTC'));

        $entity = $this
            ->queryContainer
            ->queryConcreteTimeSlotByZipCodeBranchAndTime(
                $zipCode,
                $idBranch,
                $startTime,
                $endTime
            )
            ->findOne();

        if($entity !== null){
            $this->checkIfAbstractTourToTimeSlotExists($entity->getSpyTimeSlot());
            return $entity->getIdConcreteTimeSlot();
        }

        $timeSlot = $this
            ->queryContainer
            ->queryTimeSlotForZipCodeAndBranch(
                $zipCode,
                $idBranch
            )
            ->findOne();

        if($timeSlot === null){
            $timeSlot = (new SpyTimeSlot())
                ->setFkDeliveryArea($this->getDeliveryAreaId($zipCode))
                ->setFkBranch($idBranch)
                ->setStartTime(static::DEFAULT_START_TIME_TIME_SLOT)
                ->setEndTime(static::DEFAULT_END_TIME_TIME_SLOT)
                ->setPrepTime(0)
                ->setStatus(SpyTimeSlotTableMap::COL_STATUS_DELETED);
        }

        $entity = (new SpyConcreteTimeSlot())
            ->setStartTime($startTime)
            ->setEndTime($endTime)
            ->setSpyTimeSlot($timeSlot)
            ->setIsActive(false);

        if ($entity->isNew() === true) {
            $entity->save();
            $this->counter++;
        }

        $this->checkIfAbstractTourToTimeSlotExists($timeSlot);

        return $entity->getIdConcreteTimeSlot();
    }

    /**
     * @param string $zipCode
     * @return int
     * @throws PropelException
     */
    protected function getDeliveryAreaId(string $zipCode): int
    {
        return $this
            ->queryContainer
            ->queryDeliveryAreaByZipCode($zipCode)
            ->select(SpyDeliveryAreaTableMap::COL_ID_DELIVERY_AREA)
            ->findOne();
    }

    /**
     * @param SpyTimeSlot $timeSlot
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function checkIfAbstractTourToTimeSlotExists(SpyTimeSlot $timeSlot)
    {
        $abstract = $this
            ->queryContainer
            ->queryAbstractTourToAbstractTimeSlot()
            ->filterByFkAbstractTimeSlot($timeSlot->getIdTimeSlot())
            ->filterByFkAbstractTour($this->getIntegraAbstractTourForBranch($timeSlot->getFkBranch()))
            ->findOneOrCreate();

        if($abstract->isNew() || $abstract->isModified()){
            $abstract->save();
        }
    }


    /**
     * @param int $idBranch
     * @return mixed
     */
    protected function getIntegraAbstractTourForBranch(int $idBranch){
        if(array_key_exists($idBranch, $this->integraAbstractTour) === true)
        {
            return $this->integraAbstractTour[$idBranch];
        }

        $this->integraAbstractTour[$idBranch] = $this
            ->queryContainer
            ->queryIntegraTour($idBranch)
            ->findOne();

        if ($this->integraAbstractTour[$idBranch] === null) {
            $this->integraAbstractTour[$idBranch] = $this->createIntegraTour($idBranch);
        }

        return $this->integraAbstractTour[$idBranch];
    }

    /**
     * @param int $idBranch
     * @return int
     * @throws PropelException
     */
    protected function createIntegraTour(int $idBranch): int
    {
        $entity = (new DstAbstractTour())
            ->setName(IntegraConstants::INTEGRA_TOUR_NAME)
            ->setFkBranch($idBranch)
            ->setStatus(DstAbstractTourTableMap::COL_STATUS_DEACTIVATED);

        $entity->save();

        return $entity->getIdAbstractTour();
    }
}
