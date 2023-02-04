<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\Business\Exception\GraphmastersTourAlreadyFixedException;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder\GraphmastersOrderInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

class Tour implements TourInterface
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersConfig
     */
    protected $config;

    /**
     * @var GraphmastersOrderInterface
     */
    protected $graphmastersOrderModel;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var GraphMastersSettingsInterface
     */
    protected $graphMastersSettingsModel;

    /**
     * @var TourHandlerInterface
     */
    protected $tourHandler;

    /**
     * @var TourDriverappMapperInterface
     */
    protected $tourDriverAppMapper;

    /**
     * @var OmsQueryContainerInterface
     */
    protected $omsQueryContainer;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @var IntegraFacadeInterface
     */
    protected $integraFacade;

    /**
     * @var DiscountQueryContainerInterface
     */
    protected $discountQueryContainer;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param GraphMastersConfig $config
     * @param SalesFacadeInterface $salesFacade
     * @param GraphMastersSettingsInterface $graphMastersSettingsModel
     * @param TourHandlerInterface $tourHandler
     * @param GraphmastersOrderInterface $graphmastersOrderModel
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        GraphMastersConfig $config,
        SalesFacadeInterface $salesFacade,
        GraphMastersSettingsInterface $graphMastersSettingsModel,
        TourHandlerInterface $tourHandler,
        GraphmastersOrderInterface $graphmastersOrderModel,
        TourFacadeInterface $tourFacade,
        OmsQueryContainerInterface $omsQueryContainer,
        IntegraFacadeInterface $integraFacade,
        DiscountQueryContainerInterface $discountQueryContainer,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->salesFacade = $salesFacade;
        $this->graphMastersSettingsModel = $graphMastersSettingsModel;
        $this->tourHandler = $tourHandler;
        $this->graphmastersOrderModel = $graphmastersOrderModel;
        $this->tourDriverAppMapper = $tourFacade->getTourDriverAppMapper();
        $this->omsQueryContainer = $omsQueryContainer;
        $this->tourFacade = $tourFacade;
        $this->integraFacade = $integraFacade;
        $this->discountQueryContainer = $discountQueryContainer;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @return GraphMastersTourTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function save(GraphMastersTourTransfer $tourTransfer): GraphMastersTourTransfer
    {
        $tourEntity = $this->findOrCreateEntityByOriginalId($tourTransfer->getOriginalId());

        if ($tourEntity->isNew()) {
            $tourEntity->setOriginalId($tourTransfer->getOriginalId())
                ->setFkBranch($tourTransfer->getFkBranch())
                ->setReference($tourTransfer->getReference());
        }

        $tourEntity
            ->setDate(new DateTime($tourTransfer->getDate()))
            ->setTourCommissioningCutOff($tourTransfer->getTourCommissioningCutOff())
            ->setTourStartEta($this->toUtcDateTime($tourTransfer->getTourStartEta()))
            ->setTourDestinationEta($this->toUtcDateTime($tourTransfer->getTourDestinationEta()))
            ->setTourStatus($tourTransfer->getTourStatus())
            ->setEdiGoodsExported($tourTransfer->getEdiGoodsExported())
            ->setEdiDepositExported($tourTransfer->getEdiDepositExported());

        if ($tourTransfer->getVehicleStatus() !== null) {
            $tourEntity->setVehicleStatus($tourTransfer->getVehicleStatus());
        }

        if ($tourTransfer->getTotalDistanceMeters() !== null) {
            $tourEntity->setTotalDistanceMeters($tourTransfer->getTotalDistanceMeters());
        }

        if ($tourTransfer->getTotalTimeSeconds() !== null) {
            $tourEntity->setTotalTimeSeconds($tourTransfer->getTotalTimeSeconds());
        }

        if ($tourEntity->isNew()) {
            $tourEntity->save();
        }

        $currentOrderReferences = [];

        foreach ($tourEntity->getDstGraphmastersOrders() as $graphmastersOrder) {
            $currentOrderReferences[] = $graphmastersOrder->getSpySalesOrder()->getOrderReference();
        }

        $currentGraphmastersOrderTransfers = $this
            ->graphmastersOrderModel
            ->getMultipleOrdersByFkOrderReferences($currentOrderReferences);

        /** @var GraphMastersOrderTransfer[] $newGraphmastersOrderTransfers */
        $newGraphmastersOrderTransfers = (array) $tourTransfer->getGraphmastersOrders();

        $newOrderReferences = [];

        foreach ($newGraphmastersOrderTransfers as $newGraphmastersOrderTransfer) {
            $newOrderReferences[] = $newGraphmastersOrderTransfer->getFkOrderReference();
        }

        $tourEntity->setOrderCount($newGraphmastersOrderTransfers !== null ? count($newGraphmastersOrderTransfers) : 0);

        $this->updateRelatedOrders(
            $tourEntity,
            $currentGraphmastersOrderTransfers,
            $newGraphmastersOrderTransfers,
            $currentOrderReferences,
            $newOrderReferences
        );

        $this->updateTotalWeightGrams($tourEntity, $newOrderReferences);

        if ($tourEntity->isModified()) {
            $tourEntity->save();
        }

        return $this->entityToTransfer($tourEntity);
    }

    /**
     * @param DstGraphmastersTour $tourEntity
     *
     * @return GraphMastersTourTransfer
     *
     * @throws PropelException
     */
    public function entityToTransfer(DstGraphmastersTour $tourEntity): GraphMastersTourTransfer
    {
        $tourTransfer = (new GraphMastersTourTransfer())
            ->fromArray($tourEntity->toArray(), true);

        $tourTransfer
            ->setDate($tourEntity->getDate()->format('Y-m-d'))
            ->setTourStartEta($this->toLocalDateTimeString($tourEntity->getTourStartEta()))
            ->setTourDestinationEta($this->toLocalDateTimeString($tourEntity->getTourDestinationEta()))
            ->setBranchName($tourEntity->getSpyBranch()->getName())
            ->setZipCodes([]);

        foreach ($tourEntity->getDstGraphmastersOrders() as $graphmastersOrderEntity) {
            $orderEntity = $graphmastersOrderEntity->getSpySalesOrder();

            $zipCode = $orderEntity->getShippingAddress()->getZipCode();

            if (in_array($zipCode, $tourTransfer->getZipCodes()) === false) {
                $tourTransfer->addZipCodes($zipCode);
            }

            $orderTransfer = (new OrderTransfer())
                ->setOrderReference($orderEntity->getOrderReference());

            $graphmastersOrderTransfer = (new GraphMastersOrderTransfer())
                ->setFkOrderReference($graphmastersOrderEntity->getFkOrderReference())
                ->setOrder($orderTransfer)
                ->setStopEta($this->toLocalDateTimeString($graphmastersOrderEntity->getStopEta()))
                ->setStatus($graphmastersOrderEntity->getStatus());

            $tourTransfer->addGraphmastersOrders($graphmastersOrderTransfer);
        }

        return $tourTransfer;
    }

    /**
     * @param int $idTour
     *
     * @return GraphMastersTourTransfer
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourById(int $idTour): GraphMastersTourTransfer
    {
        $tourEntity = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->findOneByIdGraphmastersTour($idTour);

        if ($tourEntity === null) {
            throw EntityNotFoundException::build($idTour);
        }

        return $this->entityToTransfer($tourEntity);
    }

    /**
     * @param int $idTour
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function fixTourById(int $idTour): void
    {
        $tour = $this->getTourById($idTour);

        if ($tour->getTourStatus() === GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_CLOSED) {
            throw new GraphmastersTourAlreadyFixedException($idTour);
        }

        $depotId = $this
            ->graphMastersSettingsModel
            ->getSettingsByIdBranch($tour->getFkBranch())
            ->getDepotApiId();

        $requestTransfer = $this
            ->tourHandler
            ->createApiToursRequestTransfer($depotId, [$tour->getOriginalId()]);

        $this->tourHandler->fixTours($requestTransfer);

        $tour->setTourStatus(GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_CLOSED);

        $this->save($tour);

        if ($tour->getEdiGoodsExported() !== true) {
            $this->ediExportTour($tour);
        }
    }

    /**
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function fixOpenToursCutoffReached() : void
    {
        $openTours = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->filterByTourCommissioningCutOff('now', Criteria::LESS_EQUAL)
            ->filterByTourStatus('idle')
            ->filterByEdiGoodsExported(false)
            ->find();

        foreach($openTours as $openTour)
        {
            $this
                ->fixTourById($openTour->getIdGraphmastersTour());
        }
    }

    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @throws PropelException
     */
    public function comment(GraphMastersTourTransfer $tourTransfer): void
    {
        $tourEntity = $this->findEntityById($tourTransfer->getIdGraphmastersTour());
        $tourEntity->setComment($tourTransfer->getComment());

        if ($tourEntity->isModified()) {
            $tourEntity->save();
        }
    }

    /**
     * @param DriverTransfer $driverTransfer
     *
     * @return array|DriverAppTourTransfer[]
     *
     * @throws PropelException
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array
    {
        $stateWhiteList = $this->getStateIds();

        $graphmastersTours = $this
            ->queryContainer
            ->queryToursHydratedForDriverApp(
                $driverTransfer,
                $this->getActiveProcessIds(),
                $stateWhiteList,
                $this->getPastTimeLimit(),
                $this->getFutureTimeLimit($driverTransfer->getFkBranch())
            )
            ->find();

        $distinctTours = $this->removeTourDuplicates($graphmastersTours);

        $skus = $this->getSkus($distinctTours);

        $this->fetchDiscountsForGraphmastersTourOrders($distinctTours);

        return $this
            ->tourDriverAppMapper
            ->mapEagerLoadedGraphmastersTourEntitiesToTransfers(
                $distinctTours,
                $skus,
                $driverTransfer,
                $stateWhiteList
            );
    }

    /**
     * @param string $tourReference
     *
     * @return GraphMastersTourTransfer
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourByReference(string $tourReference): GraphMastersTourTransfer
    {
        $tourEntity = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->findOneByReference($tourReference);

        if ($tourEntity === null) {
            throw EntityNotFoundException::reference($tourReference);
        }

        return $this->entityToTransfer($tourEntity);
    }

    /**
     * @param string $originalId
     *
     * @return GraphMastersTourTransfer|null
     *
     * @throws PropelException
     */
    public function getTourByOriginalId(string $originalId): ?GraphMastersTourTransfer
    {
        $tourEntity = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->findOneByOriginalId($originalId);

        if ($tourEntity === null) {
            return null;
        }

        return $this->entityToTransfer($tourEntity);
    }

    /**
     * @param int $fkBranch
     *
     * @return array
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function getTodaysIdleToursByFkBranch(int $fkBranch): array
    {
        $tours = $this
            ->queryContainer
            ->queryTodaysIdleToursByFkBranch($fkBranch)
            ->find();

        $tourTransfers = [];

        foreach ($tours as $tour) {
            $tourTransfers[] = $this->entityToTransfer($tour);
        }

        return $tourTransfers;
    }

    /**
     * @param int $idTour
     *
     * @throws PropelException
     */
    public function deleteTourById(int $idTour): void
    {
        $tourEntity = $this->findEntityById($idTour);

        if ($tourEntity === null) {
            throw EntityNotFoundException::build($idTour);
        }

        $tourEntity->delete();
    }

    /**
     * @param DstGraphmastersTour $tourEntity
     * @param array|GraphMastersOrderTransfer[] $currentGraphmastersOrderTransfers
     * @param array|GraphMastersOrderTransfer[] $newGraphmastersOrderTransfers
     * @param array $currentOrderReferences
     * @param array $newOrderReferences
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function updateRelatedOrders(
        DstGraphmastersTour $tourEntity,
        array $currentGraphmastersOrderTransfers,
        array $newGraphmastersOrderTransfers,
        array $currentOrderReferences,
        array $newOrderReferences
    ): void {
        foreach ($newGraphmastersOrderTransfers as $graphmastersOrderTransfer) {
            $graphmastersOrderTransfer->setFkGraphmastersTour($tourEntity->getIdGraphmastersTour());

            $this
                ->graphmastersOrderModel
                ->save($graphmastersOrderTransfer);
        }

/*
        $orderReferencesToUnassign = array_diff($currentOrderReferences, $newOrderReferences);

        $graphmastersOrderTransfersToMarkCancelled = array_filter(
            $currentGraphmastersOrderTransfers,
            function (GraphMastersOrderTransfer $graphmastersOrderTransfer) use ($orderReferencesToUnassign) {
                return in_array($graphmastersOrderTransfer->getFkOrderReference(), $orderReferencesToUnassign);
            }
        );

        foreach ($graphmastersOrderTransfersToMarkCancelled as $graphmastersOrderTransfer) {
            $this
                ->graphmastersOrderModel
                ->markOrderCancelledByReference($graphmastersOrderTransfer->getFkOrderReference());
        }
*/
    }

    /**
     * @param DstGraphmastersTour $entity
     * @param array $newOrderReferences
     *
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    protected function updateTotalWeightGrams(DstGraphmastersTour $entity, array $newOrderReferences)
    {
        $orderTransfers = $this
            ->salesFacade
            ->getMultipleOrdersWithTotalsByOrderReferences($newOrderReferences);

        $totalWeightGrams = 0;

        foreach ($orderTransfers as $orderTransfer) {
            if (in_array($orderTransfer->getOrderReference(), $newOrderReferences)) {
                $totalWeightGrams += $orderTransfer->getTotals()->getWeightTotal();
            }
        }

        $entity->setTotalWeightGrams($totalWeightGrams);
    }

    /**
     * @param string $originalId
     *
     * @return DstGraphmastersTour
     */
    protected function findOrCreateEntityByOriginalId(string $originalId): DstGraphmastersTour
    {
        $entity = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->findOneByOriginalId($originalId);

        if ($entity === null) {
            return new DstGraphmastersTour();
        }

        return $entity;
    }

    /**
     * @param int $idTour
     *
     * @return DstGraphmastersTour
     */
    protected function findEntityById(int $idTour): DstGraphmastersTour
    {
        $tourEntity = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->findOneByIdGraphmastersTour($idTour);

        if ($tourEntity === null) {
            throw EntityNotFoundException::build($idTour);
        }

        return $tourEntity;
    }

    /**
     * @return array
     */
    protected function getStateIds(): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName(
                $this->tourFacade->getConfig()->getAcceptedOmsState()
            )
            ->find();

        $states = [];

        foreach ($result as $item) {
            $states[] = $item->getIdOmsOrderItemState();
        }

        return $states;
    }

    /**
     * @return array|int[]
     */
    protected function getActiveProcessIds(): array
    {
        $activeProcesses = $this->getActiveProcesses();

        $idProcesses = [];

        /** @var SpyOmsOrderProcess $activeProcess */
        foreach ($activeProcesses as $activeProcess) {
            $idProcesses[] = $activeProcess->getIdOmsOrderProcess();
        }

        return $idProcesses;
    }

    /**
     * @return ObjectCollection
     */
    protected function getActiveProcesses(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->getActiveProcesses($this->tourFacade->getConfig()->getActiveProcesses())
            ->find();
    }

    /**
     * @return DateTime
     */
    protected function getPastTimeLimit(): DateTime
    {
        return new DateTime($this->tourFacade->getConfig()->getDriverAppTourPastCutOff());
    }

    /**
     * @param int $idBranch
     *
     * @return DateTime
     */
    protected function getFutureTimeLimit(int $idBranch): DateTime
    {
        if ($this->integraFacade->doesBranchUseIntegra($idBranch) === true){
            return new DateTime($this->tourFacade->getConfig()->getDriverAppTourFutureCutOffIntegra());
        }

        return new DateTime($this->tourFacade->getConfig()->getDriverAppTourFutureCutOff());
    }

    /**
     * @param iterable|DstGraphmastersTour[] $graphmastersTours
     *
     * @return DstGraphmastersTour[]
     */
    protected function removeTourDuplicates(iterable $graphmastersTours): array
    {
        $distinctGraphmastersTours = [];

        foreach ($graphmastersTours as $graphmastersTour) {
            $distinctGraphmastersTours[$graphmastersTour->getIdGraphmastersTour()] = $graphmastersTour;
        }

        return $distinctGraphmastersTours;
    }

    /**
     * @param iterable|DstGraphmastersTour[] $graphmastersTours
     *
     * @return array
     *
     * @throws PropelException
     */
    protected function getSkus(iterable $graphmastersTours): array
    {
        $skus = [];

        foreach ($graphmastersTours as $graphmastersTour) {
            foreach ($graphmastersTour->getDstGraphmastersOrders() as $graphmastersOrder) {
                foreach ($graphmastersOrder->getSpySalesOrder()->getItems() as $item) {
                    $skus[$item->getSku()] = $item->getSku();
                }
            }
        }

        return array_keys($skus);
    }

    /**
     * @param DstGraphmastersTour[] $graphmastersTours
     *
     * @throws PropelException
     */
    protected function fetchDiscountsForGraphmastersTourOrders(array $graphmastersTours): void
    {
        $orders = [];

        foreach ($graphmastersTours as $graphmastersTour) {
            foreach ($graphmastersTour->getDstGraphmastersOrders() as $graphmastersOrder) {
                $order = $graphmastersOrder->getSpySalesOrder();

                $orders[$order->getIdSalesOrder()] = $order;
            }
        }

        $discounts = $this
            ->discountQueryContainer
            ->queryVoucherDiscountsByOrderIds(array_keys($orders))
            ->find();

        foreach ($discounts as $discount) {
            $orders[$discount->getFkSalesOrder()]->addDiscount($discount);
        }
    }

    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function ediExportTour(GraphMastersTourTransfer $tourTransfer): void
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById($tourTransfer->getFkBranch());

        $exitCode = $this
            ->tourFacade
            ->ediExportTourById(
                $tourTransfer->getIdGraphmastersTour(),
                $branch->getEdiEndpointUrl(),
                6000,
                true
            );

        if ($exitCode === 0) {
            $tourTransfer->setEdiGoodsExported(true);

            $this->save($tourTransfer);
        }
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     */
    private function toUtcDateTime(string $dateTime): DateTime
    {
        return (new DateTime($dateTime, new DateTimeZone($this->config->getProjectTimeZone())))
            ->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * @param DateTime $dateTime
     *
     * @return string
     */
    private function toLocalDateTimeString(DateTime $dateTime): string
    {
        return $dateTime
            ->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()))
            ->format('H:i:s');
    }
}
