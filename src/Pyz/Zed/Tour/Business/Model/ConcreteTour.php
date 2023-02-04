<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 19.10.18
 * Time: 13:23
 */

namespace Pyz\Zed\Tour\Business\Model;

use ArrayObject;
use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Generated\Shared\Transfer\VehicleTypeTransfer;
use Orm\Zed\StateMachine\Persistence\Map\SpyStateMachineItemStateTableMap;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlot;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstVehicleTypeTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourExistsException;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\AvailableDriverConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\ConcreteTourHydratorInterface;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\StatusConcreteTourHydrator;
use Pyz\Zed\Tour\Communication\Plugin\StateMachine\TourStateMachineHandlerPlugin;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridgeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class ConcreteTour implements ConcreteTourInterface
{
    use LoggerTrait;

    public const AGG_SALES_ORDER_TOTAL_WEIGHT_COLUMN_NAME = 'agg_sales_order_total_weight';
    public const AGG_DELIVERY_AREA_ZIP_CODE_COLUMN_NAME = 'agg_delivery_area_zip_code';
    public const AGG_TIME_SLOT_PREP_TIME_COLUMN_NAME = 'agg_time_slot_prep_time';
    public const COUNT_SALES_ORDER_COLUMN_NAME = 'count_sales_order';
    public const MIN_CONCRETE_TIME_SLOT_START_COLUMN_NAME = 'min_concrete_time_slot_start';
    public const MAX_CONCRETE_TIME_SLOT_END_COLUMN_NAME = 'max_concrete_time_slot_end';
    public const MIN_TIME_SLOT_START_COLUMN_NAME = 'min_time_slot_start';
    public const MAX_TIME_SLOT_END_COLUMN_NAME = 'min_time_slot_end';

    protected const CONCRETE_TOUR_STATE_DELETED = 'deleted';

    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TourConfig
     */
    protected $config;

    /**
     * @var ConcreteTourHydratorInterface[]
     */
    protected $hydrators;

    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * @var \Pyz\Zed\Integra\Business\IntegraFacadeInterface
     */
    protected $integraFacade;

    /**
     * @var TourReferenceGeneratorInterface
     */
    protected $tourReferenceGenerator;

    /**
     * @var TourToStateMachineBridgeInterface
     */
    protected $tourStateMachineBridge;

    /**
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Tour\Business\Model\TourReferenceGeneratorInterface $tourReferenceGenerator
     * @param \Pyz\Zed\Tour\TourConfig $config
     * @param array $hydrators
     * @param \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface $deliveryAreaFacade
     * @param \Pyz\Zed\Integra\Business\IntegraFacadeInterface $integraFacade
     * @param \Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridgeInterface $tourStateMachineBridge
     */
    public function __construct(
        TourQueryContainerInterface $queryContainer,
        TourReferenceGeneratorInterface $tourReferenceGenerator,
        TourConfig $config,
        array $hydrators,
        DeliveryAreaFacadeInterface $deliveryAreaFacade,
        IntegraFacadeInterface $integraFacade,
        TourToStateMachineBridgeInterface $tourStateMachineBridge
    ) {
        $this->queryContainer = $queryContainer;
        $this->tourReferenceGenerator = $tourReferenceGenerator;
        $this->config = $config;
        $this->hydrators = $hydrators;
        $this->deliveryAreaFacade = $deliveryAreaFacade;
        $this->integraFacade = $integraFacade;
        $this->tourStateMachineBridge = $tourStateMachineBridge;
    }

    /**
     * @param int $idConcreteTour
     *
     * @return ConcreteTourTransfer
     *@return ConcreteTourTransfer
     *@throws ConcreteTourNotExistsException
     *
     */
    public function getConcreteTourById(int $idConcreteTour) : ConcreteTourTransfer
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTourById($idConcreteTour)
            ->findOne();

        if ($concreteTourEntity === null) {
            throw new ConcreteTourNotExistsException(
                sprintf(ConcreteTourNotExistsException::ID_NOT_EXISTS_MESSAGE, $idConcreteTour)
            );
        }

        return $this->entityToTransfer($concreteTourEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $idsConcreteTour
     * @return array
     * @throws AmbiguousComparisonException
     */
    public function getConcreteToursByIds(array $idsConcreteTour): array
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTour()
            ->joinWithState(Criteria::LEFT_JOIN)
            ->filterByIdConcreteTour($idsConcreteTour, Criteria::IN)
            ->find();

        $transfers = [];
        foreach ($concreteTourEntity as $entity) {
            $transfers[$entity->getIdConcreteTour()] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return ConcreteTourTransfer
     */
    public function createConcreteTourForConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTourTransfer
    {
        if ($concreteTimeSlotTransfer->getFkConcreteTour() !== null) {

            return $this->getConcreteTourById($concreteTimeSlotTransfer->getFkConcreteTour());
        }

        $abstractTourIds = $this->getAbstractTourIdsForConcreteTimeSlot($concreteTimeSlotTransfer);
        if (count($abstractTourIds) === 0) {
            return new ConcreteTourTransfer();
        }

        $concreteTourEntity = $this->findOrCreateEntityByConcreteTimeSlot($concreteTimeSlotTransfer);

        return $this->save($this->entityToTransfer($concreteTourEntity));
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     *
     * @return ConcreteTourTransfer
     */
    public function save(ConcreteTourTransfer $concreteTourTransfer) : ConcreteTourTransfer
    {
        $this->checkAssertions($concreteTourTransfer);

        $concreteTourEntity = $this->findEntityOrCreate($concreteTourTransfer);

        $isNew = $concreteTourEntity->isNew();

        $concreteTourEntity->fromArray($concreteTourTransfer->toArray());
        $this->checkUnique($concreteTourEntity);

        if ($isNew === true) {
            $concreteTourEntity->setTourReference($this
                ->tourReferenceGenerator
                ->generateTourReference($concreteTourTransfer));
        }

        if ($isNew === true || $concreteTourEntity->isModified() === true) {
            $concreteTourEntity->save();
            $concreteTourTransfer->setIdConcreteTour($concreteTourEntity->getIdConcreteTour());
        }

        if (
            $isNew === true &&
            $this->doesBranchUseIntegra($concreteTourTransfer) !== true
        ) {
            $this
                ->createPreparationAndStartForConcreteTours();
        }

        return $concreteTourTransfer;
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     *
     * @return ConcreteTourTransfer
     */
    public function comment(ConcreteTourTransfer $concreteTourTransfer) : ConcreteTourTransfer
    {
        $toCommentConcreteTourTransfer = $this
            ->getConcreteTourById($concreteTourTransfer->getIdConcreteTour());

        $toCommentConcreteTourTransfer->setComment($concreteTourTransfer->getComment());
        return $this->save($toCommentConcreteTourTransfer);
    }

    /**
     * @return void
     */
    public function generateAllConcreteToursForExistingConcreteTimeSlotsInFuture() : void
    {
        $concreteTimeSlotTransfers = $this
            ->deliveryAreaFacade
            ->getConcreteTimeSlotsInFutureWithoutConcreteTour();

        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {

            $concreteTourTransfer = $this
                ->createConcreteTourForConcreteTimeSlot($concreteTimeSlotTransfer);

            if ($concreteTourTransfer->getIdConcreteTour() !== null &&
                ($concreteTourTransfer->getState() === null ||
                    $concreteTourTransfer->getState() === TourConstants::TOUR_STATE_ORDERABLE ||
                    $concreteTourTransfer->getState() === TourConstants::TOUR_STATE_NEW)) {
                $concreteTimeSlotTransfer->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
                $this->deliveryAreaFacade->setFkConcreteTourInConcreteTimeSlot($concreteTimeSlotTransfer);
            }
        }
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     *
     * @return ConcreteTourTransfer
     */
    public function entityToTransfer(DstConcreteTour $concreteTourEntity) : ConcreteTourTransfer
    {
        $concreteTourTransfer = new ConcreteTourTransfer();
        $concreteTourTransfer->fromArray($concreteTourEntity->toArray(), true);

        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrateConcreteTour($concreteTourEntity, $concreteTourTransfer);
        }

        return $concreteTourTransfer;
    }

    /**
     * @param array $concreteTourEntityArray
     *
     * @return ConcreteTourTransfer
     *
     * @throws PropelException
     */
    public function entityArrayToTransferForIndex(array $concreteTourEntityArray): ConcreteTourTransfer
    {
        $concreteTourTransfer = (new ConcreteTourTransfer())
            ->setIdConcreteTour($concreteTourEntityArray[DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR])
            ->setTourReference($concreteTourEntityArray[DstConcreteTourTableMap::COL_TOUR_REFERENCE])
            ->setDate($concreteTourEntityArray[DstConcreteTourTableMap::COL_DATE])
            ->setStartTime($concreteTourEntityArray[ConcreteTour::MIN_TIME_SLOT_START_COLUMN_NAME])
            ->setEndTime($concreteTourEntityArray[ConcreteTour::MAX_TIME_SLOT_END_COLUMN_NAME])
            ->setOrderCount($concreteTourEntityArray[ConcreteTour::COUNT_SALES_ORDER_COLUMN_NAME])
            ->setIsCommissioned($concreteTourEntityArray[DstConcreteTourTableMap::COL_IS_COMMISSIONED])
            ->setFkBranch($concreteTourEntityArray[DstConcreteTourTableMap::COL_FK_BRANCH]);

        if (isset($concreteTourEntityArray[SpyStateMachineItemStateTableMap::COL_NAME])) {
            $concreteTourTransfer->setState($concreteTourEntityArray[SpyStateMachineItemStateTableMap::COL_NAME]);
        }

        if (isset($concreteTourEntityArray[DstConcreteTourTableMap::COL_FK_DRIVER])) {
            $concreteTourTransfer->setFkDriver($concreteTourEntityArray[DstConcreteTourTableMap::COL_FK_DRIVER]);
        }

        $abstractTourTransfer = $this->createAbstractTourTransfer($concreteTourEntityArray);

        $concreteTourWeightKg = $this->determineConcreteTourWeightKg($concreteTourEntityArray);

        $concreteTourTransfer
            ->setAbstractTour($abstractTourTransfer)
            ->setWeightKg($concreteTourWeightKg);

        foreach ($this->hydrators as $hydrator) {
            if (get_class($hydrator) === StatusConcreteTourHydrator::class) {
                $hydrator->hydrateConcreteTour(
                    (new DstConcreteTour())->setDate(new DateTime($concreteTourEntityArray[DstConcreteTourTableMap::COL_DATE])),
                    $concreteTourTransfer
                );
            }

            if (get_class($hydrator) === AvailableDriverConcreteTourHydrator::class) {
                $hydrator->hydrateConcreteTour(new DstConcreteTour(), $concreteTourTransfer);
            }
        }

        return $concreteTourTransfer;
    }

    /**
     * @param array $concreteTourEntityArray
     *
     * @return AbstractTourTransfer
     */
    protected function createAbstractTourTransfer(array $concreteTourEntityArray): AbstractTourTransfer
    {
        $abstractTourTransfer = (new AbstractTourTransfer())
            ->setIdAbstractTour($concreteTourEntityArray[DstAbstractTourTableMap::COL_ID_ABSTRACT_TOUR])
            ->setName($concreteTourEntityArray[DstAbstractTourTableMap::COL_NAME])
            ->setWeekday(DstAbstractTourTableMap::getValueSet(DstAbstractTourTableMap::COL_WEEKDAY)[$concreteTourEntityArray[DstAbstractTourTableMap::COL_WEEKDAY]])
            ->setStartTime($concreteTourEntityArray[ConcreteTour::MIN_TIME_SLOT_START_COLUMN_NAME])
            ->setEndTime($concreteTourEntityArray[ConcreteTour::MAX_TIME_SLOT_END_COLUMN_NAME]);

        $deliveryAreaTransfers = $this->createDeliveryAreaTransfers($concreteTourEntityArray);

        $vehicleTypeTransfer = $this->createVehicleTypeTransfer($concreteTourEntityArray);

        $abstractTourPrepTimeBufferMinutesBeforeStart = $this
            ->determineAbstractTourPrepTimeBufferMinutesBeforeStart($concreteTourEntityArray);

        $preparationTime = sprintf(
            '%02d:%02d',
            $abstractTourPrepTimeBufferMinutesBeforeStart / 60,
            $abstractTourPrepTimeBufferMinutesBeforeStart % 60
        );

        $abstractTourTransfer
            ->setDeliveryAreas($deliveryAreaTransfers)
            ->setVehicleType($vehicleTypeTransfer)
            ->setPreparationTime($preparationTime)
            ->setPrepTimeBufferMinutesBeforeStart($abstractTourPrepTimeBufferMinutesBeforeStart);

        return $abstractTourTransfer;
    }

    /**
     * @param array $concreteTourEntityArray
     *
     * @return ArrayObject|DeliveryAreaTransfer[]
     */
    protected function createDeliveryAreaTransfers(array $concreteTourEntityArray): ArrayObject
    {
        $deliveryAreaZipCodes = json_decode(
            $concreteTourEntityArray[ConcreteTour::AGG_DELIVERY_AREA_ZIP_CODE_COLUMN_NAME],
            true
        );

        $deliveryAreaTransfers = new ArrayObject();

        foreach ($deliveryAreaZipCodes as $deliveryAreaZipCode) {
            if ($deliveryAreaZipCode === null) {
                continue;
            }

            $deliveryAreaTransfers->append(
                (new DeliveryAreaTransfer())->setZip($deliveryAreaZipCode)
            );
        }

        return $deliveryAreaTransfers;
    }

    /**
     * @param array $concreteTourEntityArray
     *
     * @return VehicleTypeTransfer
     */
    protected function createVehicleTypeTransfer(array $concreteTourEntityArray): VehicleTypeTransfer
    {
        $vehicleTypeTransfer = (new VehicleTypeTransfer())
            ->setName($concreteTourEntityArray[DstVehicleTypeTableMap::COL_NAME])
            ->setPayloadKg($concreteTourEntityArray[DstVehicleTypeTableMap::COL_PAYLOAD_KG]);

        return $vehicleTypeTransfer;
    }

    protected function determineAbstractTourPrepTimeBufferMinutesBeforeStart(array $concreteTourEntityArray): int
    {
        $timeSlotPrepTimes = json_decode(
            $concreteTourEntityArray[ConcreteTour::AGG_TIME_SLOT_PREP_TIME_COLUMN_NAME],
            true
        );

        $abstractTourPrepTimeBufferMinutesBeforeStart = $timeSlotPrepTimes[0]['prep_time'] ?? 0;

        foreach($timeSlotPrepTimes as $timeSlotPrepTime) {
            if (isset($timeSlotPrepTime['prep_time']) &&
                $timeSlotPrepTime['prep_time'] < $abstractTourPrepTimeBufferMinutesBeforeStart
            ) {
                $abstractTourPrepTimeBufferMinutesBeforeStart = $timeSlotPrepTime['prep_time'];
            }
        }

        return $abstractTourPrepTimeBufferMinutesBeforeStart;
    }

    /**
     * @param array $concreteTourEntityArray
     *
     * @return int
     */
    protected function determineConcreteTourWeightKg(array $concreteTourEntityArray): int
    {
        $salesOrderTotalWeights = json_decode(
            $concreteTourEntityArray[ConcreteTour::AGG_SALES_ORDER_TOTAL_WEIGHT_COLUMN_NAME],
            true
        );

        $concreteTourWeightKg = 0;

        foreach ($salesOrderTotalWeights as $salesOrderTotalWeight) {
            $concreteTourWeightKg += $salesOrderTotalWeight['weight_total'] / 1000;
        }

        return round($concreteTourWeightKg);
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     *
     * @return DstConcreteTour
     */
    protected function findEntityOrCreate(ConcreteTourTransfer $concreteTourTransfer) : DstConcreteTour
    {
        if ($concreteTourTransfer->getIdConcreteTour() === null) {
            return new DstConcreteTour();
        }

        return $this
            ->queryContainer
            ->queryConcreteTourById($concreteTourTransfer->getIdConcreteTour())
            ->findOneOrCreate();
    }

    /**
     * @param DstConcreteTour $entity
     *
     * @throws ConcreteTourExistsException
     *
     */
    protected function checkUnique(DstConcreteTour $entity) : void
    {
        if ($entity->isNew() && ($entity->getIdConcreteTour() !== null)) {
            throw new ConcreteTourExistsException(
                sprintf(
                    ConcreteTourExistsException::ID_EXISTS_MESSAGE,
                    $entity->getIdConcreteTour()
                )
            );
        }
    }

    /**
     * @param ConcreteTourTransfer $abstractTourTransfer
     *
     * @return void
     */
    protected function checkAssertions(ConcreteTourTransfer $abstractTourTransfer) : void
    {

        $abstractTourTransfer->requireFkBranch();
        $abstractTourTransfer->requireFkAbstractTour();
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return int[]
     */
    protected function getAbstractTourIdsForConcreteTimeSlot(
        ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
    ) : array {
        $start = DateTime::createFromFormat($this->config->getDateTimeFormat(), $concreteTimeSlotTransfer->getStartTime());
        $weekDay = $this->config::DAYS_MAP[$start->format('D')];

        $idAbstractTimeSlot = $concreteTimeSlotTransfer->getFkTimeSlot();
        $entries = $this
            ->queryContainer
            ->queryAbstractTourToAbstractTimeSlot()
            ->filterByFkAbstractTimeSlot($idAbstractTimeSlot)
            ->useDstAbstractTourQuery()
                ->filterByStatus(DstAbstractTourTableMap::COL_STATUS_ACTIVE)
                ->filterByWeekday($weekDay)
            ->endUse()
            ->find();

        $abstractTourIds = [];
        foreach ($entries as $abstractTourToAbstractTimeSlot) {
            $abstractTourIds[] = $abstractTourToAbstractTimeSlot->getFkAbstractTour();
        }

        return $abstractTourIds;
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return DstConcreteTour
     */
    protected function findOrCreateEntityByConcreteTimeSlot(
        ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
    ): DstConcreteTour {

        $start = DateTime::createFromFormat($this->config->getDateTimeFormat(), $concreteTimeSlotTransfer->getStartTime());
        $dateString = $start->format($this->config->getDateFormat());
        $abstractTourIds = $this->getAbstractTourIdsForConcreteTimeSlot($concreteTimeSlotTransfer);

        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTour()
            ->useStateQuery()
                ->filterByName(self::CONCRETE_TOUR_STATE_DELETED, Criteria::NOT_EQUAL)
            ->endUse()
            ->filterByDate($dateString)
            ->filterByFkAbstractTour_In($abstractTourIds)
            ->findOne();


        if($concreteTourEntity === null)
        {
            $concreteTourEntity = new DstConcreteTour();
            $concreteTourEntity
                ->setDate($dateString);
        }

        if ($concreteTourEntity->isNew()) {
            $concreteTourEntity->setFkBranch($concreteTimeSlotTransfer->getIdBranch());
            if ($abstractTourIds !== []) {
                $concreteTourEntity->setFkAbstractTour($abstractTourIds[0]);
            }
           $concreteTourEntity->setFkStateMachineItemState(null);
        }

        return $concreteTourEntity;
    }

    /**
     * @param int $idConcreteTour
     *
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForExport(int $idConcreteTour): ConcreteTourTransfer
    {
        $concreteTourTransfer = $this
            ->getConcreteTourById($idConcreteTour);

        if ($concreteTourTransfer->getIdConcreteTour() !== null) {
            $concreteTourTransfer
                ->setExportable(true);

            $this
                ->save($concreteTourTransfer);
        }

        return $concreteTourTransfer;
    }

    /**
     * @param int $idConcreteTour
     *
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForBeingExported(int $idConcreteTour): ConcreteTourTransfer
    {
        $concreteTourTransfer = $this
            ->getConcreteTourById($idConcreteTour);

        if ($concreteTourTransfer->getIdConcreteTour() !== null) {
            $concreteTourTransfer
                ->setExportable(false);

            $this
                ->save($concreteTourTransfer);
        }

        return $concreteTourTransfer;
    }

    /**
     * @param int $idConcreteTour
     *
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForCommissioned(int $idConcreteTour): ConcreteTourTransfer
    {
        $concreteTourTransfer = $this
            ->getConcreteTourById($idConcreteTour);

        if ($concreteTourTransfer->getIdConcreteTour() !== null) {
            $concreteTourTransfer
                ->setIsCommissioned(true);

            $this
                ->save($concreteTourTransfer);
        }

        return $concreteTourTransfer;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $tourReference
     * @return ConcreteTourTransfer
     * @throws ConcreteTourNotExistsException
     */
    public function getConcreteTourByTourReference(string $tourReference): ConcreteTourTransfer
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTour()
            ->filterByTourReference($tourReference)
            ->findOne();

        if ($concreteTourEntity->getIdConcreteTour() === null) {
            throw new ConcreteTourNotExistsException(
                sprintf(
                    ConcreteTourNotExistsException::TOUR_REFERENCE_NOT_EXISTS_MESSAGE,
                    $tourReference
                )
            );
        }

        return $this
            ->entityToTransfer($concreteTourEntity);
    }

    /**
     * @param DriverTransfer $driverTransfer
     *
     * @return ConcreteTourTransfer[]
     */
    public function getConcreteToursByDriver(DriverTransfer $driverTransfer): array
    {
        $now = new DateTime('+1day midnight');

        $concreteTourEntities = $this
            ->queryContainer
            ->queryConcreteTour()
            ->filterByDate($now, Criteria::LESS_THAN)
            ->filterByFkBranch($driverTransfer->getFkBranch())
            ->filterByFkDriver($driverTransfer->getIdDriver())
            ->addOr(DstConcreteTourTableMap::COL_FK_DRIVER, null, Criteria::ISNULL)
            ->orderByTourReference()
            ->find();

        $concreteTourTransfers = [];
        foreach ($concreteTourEntities as $concreteTourEntity) {
            $concreteTourTransfers[] = $this->entityToTransfer($concreteTourEntity);
        }

        return $concreteTourTransfers;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function isConcreteTourCommissioned(int $idConcreteTour): bool
    {
        $concreteTour = $this
            ->getConcreteTourById($idConcreteTour);

        return ($concreteTour->getIsCommissioned() === true);
    }

    /**
     * @param int $fkBranch
     * @param ConcreteTourTransfer[]
     * @return array
     * @throws AmbiguousComparisonException|PropelException
     */
    public function getConcreteTourDatesByFkBranch(int $fkBranch): array
    {
        return $this->queryContainer
            ->queryConcreteToursByFkBranch($fkBranch)
            ->groupByDate()
            ->orderByDate(Criteria::ASC)
            ->select('date')
            ->find()
            ->toArray();
    }

    /**
     * @param int $idConcreteTour
     *
     * @return ConcreteTourTransfer
     *
     * @throws ConcreteTourNotExistsException
     */
    public function flagConcreteTourForForcedEmptyExport(int $idConcreteTour): ConcreteTourTransfer
    {
        $concreteTourTransfer = $this->getConcreteTourById($idConcreteTour);

        if ($concreteTourTransfer->getIdConcreteTour() !== null) {
            $concreteTourTransfer->setForceEmptyExport(true);

            $this->save($concreteTourTransfer);
        }

        return $concreteTourTransfer;
    }

    /**
     *
     * @return void
     */
    protected function createPreparationAndStartForConcreteTours(): void
    {
        $currentTime = new DateTime('now');

        $concreteTourEntities = $this
            ->queryContainer
            ->queryConcreteTour()
            ->filterByPreparationStart(null, Criteria::ISNULL)
            ->filterByPrepTime(null, Criteria::ISNULL)
            ->filterByDate($currentTime, Criteria::GREATER_EQUAL)
            ->find();

        foreach ($concreteTourEntities as $concreteTourEntity) {
            $success = $this
                ->handleTourStateMachine($concreteTourEntity->getIdConcreteTour());

            if ($success !== true) {
                $this
                    ->getLogger()
                    ->error(sprintf(
                        'Could not set initial state to tour %d.',
                        $concreteTourEntity->getIdConcreteTour()
                    ));
            }
        }
    }

    /**
     * @param int $idConcreteTour
     *
     * @return bool
     * @throws ConcreteTourNotExistsException
     *
     */
    protected function handleTourStateMachine(int $idConcreteTour): bool
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTourById($idConcreteTour)
            ->findOne();

        if ($concreteTourEntity->getIdConcreteTour() === null) {
            throw new ConcreteTourNotExistsException(
                sprintf(
                    ConcreteTourNotExistsException::ID_NOT_EXISTS_MESSAGE,
                    $idConcreteTour
                )
            );
        }

        $utcTimeZone = new DateTimeZone('UTC');

        $currentTime = new DateTime('now');
        $currentTime
            ->setTimezone($utcTimeZone);

        $tourPreparation = $this
            ->getTourPreparationStartFromEntity($concreteTourEntity);
        $tourPrepTime = $this
            ->getTourPrepTimeFromEntity($concreteTourEntity);
        $deliveryStart = $this
            ->getTimeSlotStartTimeForTour($concreteTourEntity);

        if ($tourPreparation === null ||
            $tourPrepTime === null ||
            $deliveryStart === null ||
            $tourPreparation < $currentTime
        ) {
            return false;
        }

        $connection = $this
            ->queryContainer
            ->getConnection();

        $connection
            ->beginTransaction();

        $affectedRows = 0;

        try {
            $concreteTourEntity
                ->setPreparationStart($tourPreparation)
                ->setPrepTime($tourPrepTime)
                ->setDeliveryStart($deliveryStart);

            $affectedRows = $concreteTourEntity
                ->save();

            $this
                ->tourStateMachineBridge
                ->triggerForNewStateMachineItem(
                    $this->createProcessTransfer(),
                    $idConcreteTour
                );

            $this
                ->queryContainer
                ->getConnection()
                ->commit();
        } catch (PropelException $exception) {
            $connection
                ->rollBack();
        }

        return ($affectedRows === 1);
    }

    /**
     * @return StateMachineProcessTransfer
     */
    protected function createProcessTransfer(): StateMachineProcessTransfer
    {
        return (new StateMachineProcessTransfer())
            ->setProcessName($this->config->getStateMachineProcess())
            ->setStateMachineName(TourStateMachineHandlerPlugin::STATE_MACHINE_NAME);
    }

    /**
     * @param DstConcreteTour $dstConcreteTour
     *
     * @return DateTime|null
     */
    protected function getTourPreparationStartFromEntity(DstConcreteTour $dstConcreteTour): ?DateTime
    {
        if ($dstConcreteTour->getIdConcreteTour() === null) {
            return null;
        }

        $startTimes = [];
        foreach ($this->getAbstractTimeSlotsForTour($dstConcreteTour) as $abstractTourTimeSlot) {
            $startTime = $this
                ->createUtcDateTimeFromLocalDateAndTime(
                    $dstConcreteTour->getDate(),
                    $abstractTourTimeSlot
                        ->getSpyTimeSlot()
                        ->getStartTime()
                );

            $prepTime = $this
                ->createDateInterval(
                    $abstractTourTimeSlot
                        ->getSpyTimeSlot()
                        ->getPrepTime()
                );

            $startTimes[] = $startTime
                ->sub($prepTime);
        }

        if (empty($startTimes) === true) {
            return null;
        }

        return min($startTimes);
    }

    /**
     * @param DstConcreteTour $dstConcreteTour
     *
     * @return DstAbstractTourToAbstractTimeSlot[]
     */
    protected function getAbstractTimeSlotsForTour(DstConcreteTour $dstConcreteTour): iterable
    {
        return $dstConcreteTour
            ->getDstAbstractTour()
            ->getDstAbstractTourToAbstractTimeSlotsJoinSpyTimeSlot();
    }

    /**
     * @param DstConcreteTour $dstConcreteTour
     *
     * @return DateTime|null
     */
    protected function getTimeSlotStartTimeForTour(DstConcreteTour $dstConcreteTour): ?DateTime
    {
        $startTimes = [];
        foreach ($this->getAbstractTimeSlotsForTour($dstConcreteTour) as $abstractTourTimeSlot) {
            $startTime = $abstractTourTimeSlot
                ->getSpyTimeSlot()
                ->getStartTime();

            $startTimes[] = $this
                ->createUtcDateTimeFromLocalDateAndTime(
                    $dstConcreteTour->getDate(),
                    $startTime
                );
        }

        if (empty($startTimes) === true) {
            return null;
        }

        return min($startTimes);
    }

    /**
     * @param DstConcreteTour $dstConcreteTour
     *
     * @return int|null
     */
    protected function getTourPrepTimeFromEntity(DstConcreteTour $dstConcreteTour): ?int
    {
        if ($dstConcreteTour->getIdConcreteTour() === null) {
            return null;
        }

        $prepTimes = [];
        foreach ($this->getAbstractTimeSlotsForTour($dstConcreteTour) as $abstractTourTimeSlot) {
            $prepTimes[] = $abstractTourTimeSlot
                ->getSpyTimeSlot()
                ->getPrepTime();
        }

        if (empty($prepTimes) === true) {
            return null;
        }

        $minPrepTime = min($prepTimes);

        if ($minPrepTime < 0) {
            $minPrepTime = 0;
        }

        return $minPrepTime;
    }

    /**
     * @param int $minutes
     *
     * @return DateInterval
     */
    protected function createDateInterval(int $minutes): DateInterval
    {
        $dateIntervalString = sprintf(
            TourConfig::TIME_INTERVAL_TEMPLATE,
            $minutes
        );

        return new DateInterval($dateIntervalString);
    }

    /**
     * @param DateTime $date
     * @param DateTime $time
     *
     * @return DateTime
     */
    protected function createUtcDateTimeFromLocalDateAndTime(DateTime $date, DateTime $time): DateTime
    {
        $year = $date
            ->format('Y');
        $month = $date
            ->format('m');
        $day = $date
            ->format('d');

        $hour = $time
            ->format('H');
        $minute = $time
            ->format('i');
        $second = $time
            ->format('s');

        $projectTimezone = new DateTimeZone(
            $this->config->getProjectTimeZone()
        );

        $utcTimezone = new DateTimeZone('UTC');

        $currentDate = (new DateTime('now'))
            ->setTimezone(
                $projectTimezone
            );

        $currentDate
            ->setDate(
                $year,
                $month,
                $day
            )
            ->setTime(
                $hour,
                $minute,
                $second
            )
            ->setTimezone($utcTimezone);

        return $currentDate;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @param string $status
     * @return void
     */
    public function updateConcreteTourDepositEdiStatus(int $idConcreteTour, string $status): void
    {
        $concreteTourTransfer = $this
            ->getConcreteTourById($idConcreteTour);

        if ($concreteTourTransfer->getIdConcreteTour() !== null) {
            $concreteTourTransfer
                ->setDepositEdiStatus($status);

            $this
                ->save($concreteTourTransfer);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function triggerManualDepositEdiExport(int $idConcreteTour): bool
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTourById($idConcreteTour)
            ->findOne();

        if ($concreteTourEntity->getIdConcreteTour() === null) {
            return false;
        }

        $stateMachineItemTransfer = $this
            ->createStateMachineItemTransfer($concreteTourEntity);

        $affectedItems = $this
            ->tourStateMachineBridge
            ->triggerEvent(
                TourConstants::TOUR_STATE_EVENT_EXPORT_RETURN_MANUAL,
                $stateMachineItemTransfer
            );

        return ($affectedItems > 0);
    }

    /**
     * @param DstConcreteTour $concreteTour
     *
     * @return StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(DstConcreteTour $concreteTour): StateMachineItemTransfer
    {
        $process = $concreteTour
            ->getState()
            ->getProcess();

        $stateMachineItemTransfer = (new StateMachineItemTransfer())
            ->setIdentifier($concreteTour->getIdConcreteTour())
            ->setIdItemState($concreteTour->getFkStateMachineItemState())
            ->setProcessName($process->getName())
            ->setStateMachineName($process->getStateMachineName());

        return $stateMachineItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $concreteTourTransfer
     * @return bool
     */
    protected function doesBranchUseIntegra(
        ConcreteTourTransfer $concreteTourTransfer
    ): bool
    {
        return $this
            ->integraFacade
            ->doesBranchUseIntegra(
                $concreteTourTransfer
                    ->getFkBranch()
            );
    }
}
