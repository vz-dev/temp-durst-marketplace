<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 10:36
 */

namespace Pyz\Zed\Tour\Persistence;

use DateTime;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyConcreteTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemStateQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcessQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderAddressTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTotalsTableMap;
use Orm\Zed\StateMachine\Persistence\Map\SpyStateMachineItemStateTableMap;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourExportQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery;
use Orm\Zed\Tour\Persistence\DstVehicleCategoryQuery;
use Orm\Zed\Tour\Persistence\DstVehicleQuery;
use Orm\Zed\Tour\Persistence\DstVehicleTypeQuery;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstVehicleCategoryTableMap;
use Orm\Zed\Tour\Persistence\Map\DstVehicleTableMap;
use Orm\Zed\Tour\Persistence\Map\DstVehicleTypeTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\Tour\Business\Model\ConcreteTour;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method TourPersistenceFactory getFactory()
 */
class TourQueryContainer extends AbstractQueryContainer implements TourQueryContainerInterface
{
    /**
     * @return DstVehicleTypeQuery
     */
    public function queryVehicleType() : DstVehicleTypeQuery
    {
        return $this
            ->getFactory()
            ->createVehicleTypeQuery();
    }

    /**
     * @param int $idVehicleType
     *
     * @return DstVehicleTypeQuery
     */
    public function queryVehicleTypeById(int $idVehicleType) : DstVehicleTypeQuery
    {
        return $this
            ->queryVehicleType()
            ->filterByIdVehicleType($idVehicleType);
    }

    /**
     * @return DstVehicleCategoryQuery
     */
    public function queryVehicleCategory() : DstVehicleCategoryQuery
    {
        return $this
            ->getFactory()
            ->createVehicleCategoryQuery();
    }

    /**
     * @return DstVehicleCategoryQuery
     */
    public function queryVehicleCategoryActive() : DstVehicleCategoryQuery
    {
        return $this
            ->queryVehicleCategory()
            ->filterByStatus(DstVehicleCategoryTableMap::COL_STATUS_ACTIVE);
    }

    /**
     * @return DstAbstractTourQuery
     */
    public function queryAbstractTour() : DstAbstractTourQuery
    {
        return $this
            ->getFactory()
            ->createAbstractTourQuery();
    }

    /**
     * @return DstConcreteTourQuery
     */
    public function queryConcreteTour() : DstConcreteTourQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTourQuery();
    }

    /**
     * @return DstVehicleQuery
     */
    public function queryVehicle() : DstVehicleQuery
    {
        return $this
            ->getFactory()
            ->createVehicleQuery();
    }

    /**
     * @param int $idVehicle
     *
     * @return DstVehicleQuery
     */
    public function queryVehicleById(int $idVehicle) : DstVehicleQuery
    {
        return $this
            ->queryVehicle()
            ->filterByIdVehicle($idVehicle);
    }

    /**
     * @return DstVehicleQuery
     */
    public function queryVehicleActive(): DstVehicleQuery
    {
        return $this
            ->queryVehicle()
            ->filterByStatus(DstVehicleTableMap::COL_STATUS_ACTIVE);
    }

    /**
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicence() : DstDrivingLicenceQuery
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceQuery();
    }

    /**
     * @param int $idDrivingLicence
     *
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicenceById(int $idDrivingLicence) : DstDrivingLicenceQuery
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceQuery()
            ->filterByIdDrivingLicence($idDrivingLicence);
    }

    /**
     * @param string $code
     *
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicenceByCode(string $code) : DstDrivingLicenceQuery
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceQuery()
            ->filterByCode($code);
    }

    /**
     * @param int $idAbstractTour
     *
     * @return DstAbstractTourQuery
     */
    public function queryAbstractTourById(int $idAbstractTour) : DstAbstractTourQuery
    {
        return $this
            ->getFactory()
            ->createAbstractTourQuery()
            ->filterByIdAbstractTour($idAbstractTour);
    }

    /**
     * @param int $idConcreteTour
     *
     * @return DstConcreteTourQuery
     */
    public function queryConcreteTourById(int $idConcreteTour) : DstConcreteTourQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTourQuery()
            ->filterByIdConcreteTour($idConcreteTour);
    }

    /**
     * @return DstAbstractTourToAbstractTimeSlotQuery
     */
    public function queryAbstractTourToAbstractTimeSlot() : DstAbstractTourToAbstractTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->createAbstractTourToAbstractTimeSlotQuery();
    }

    /**
     * @param array $orderItemStates
     *
     * @return SpyOmsOrderItemStateQuery
     */
    public function querySalesOrderItemStatesByName(array $orderItemStates) : SpyOmsOrderItemStateQuery
    {
        return $this->getFactory()
            ->createOmsOrderItemStateQuery()
            ->filterByName($orderItemStates, Criteria::IN);
    }

    /**
     * @param array $processes
     *
     * @return SpyOmsOrderProcessQuery
     */
    public function querySalesOrderProcessesByName(array $processes) : SpyOmsOrderProcessQuery
    {
        return $this->getFactory()
            ->createOmsOrderProcessQuery()
            ->filterByName($processes, Criteria::IN);
    }

    /**
     * @return DstConcreteTourExportQuery
     */
    public function queryConcreteTourExport(): DstConcreteTourExportQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTourExportQuery();
    }

    /**
     * @param int $idConcreteTourExport
     *
     * @return DstConcreteTourExportQuery
     */
    public function queryConcreteTourExportById(int $idConcreteTourExport): DstConcreteTourExportQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTourExportQuery()
            ->filterByIdConcreteTourExport($idConcreteTourExport);
    }

    /**
     * {@inheritDoc}
     *
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return DstConcreteTourQuery
     */
    public function querySimultaneousConcreteToursByConcreteTour(ConcreteTourTransfer $concreteTourTransfer): DstConcreteTourQuery
    {
        return $this
            ->queryConcreteTour()
            ->joinWith('DstAbstractTour dat')
            ->joinWith('SpyConcreteTimeSlot scts')
            ->filterByFkBranch($concreteTourTransfer->getFkBranch())
            ->filterByFkDriver(null, Criteria::ISNOTNULL)
            ->filterByIdConcreteTour($concreteTourTransfer->getIdConcreteTour(), Criteria::NOT_EQUAL)
            ->filterByDate($concreteTourTransfer->getDate());
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $stateIds
     * @return DstConcreteTourQuery
     */
    public function queryStateMachineItemsByStateIds(array $stateIds = []): DstConcreteTourQuery
    {
        return $this
            ->queryConcreteTour()
            ->filterByFkStateMachineItemState_In($stateIds);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idOrder
     * @return DstConcreteTourQuery
     */
    public function queryConcreteTourByIdOrder(int $idOrder): DstConcreteTourQuery
    {
        return $this
            ->queryConcreteTour()
            ->useSpyConcreteTimeSlotQuery()
                ->useSpySalesOrderQuery()
                    ->filterByIdSalesOrder($idOrder)
                ->endUse()
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @param DriverTransfer $driverTransfer
     * @param array $processIdWhiteList
     * @param array $stateIdWhiteList
     * @param DateTime $newerThan
     * @param DateTime $olderThan
     *
     * @return DstConcreteTourQuery
     */
    public function queryToursHydratedForDriverApp(
        DriverTransfer $driverTransfer,
        array $processIdWhiteList,
        array $stateIdWhiteList,
        DateTime $newerThan,
        DateTime $olderThan
    ): DstConcreteTourQuery {

        return $this
            ->queryConcreteTour()
            ->filterByFkDriver(null, Criteria::ISNULL)
            ->_or()
            ->filterByFkDriver($driverTransfer->getIdDriver())
            ->filterByDate($olderThan, Criteria::LESS_THAN)
            ->filterByDate($newerThan, Criteria::GREATER_EQUAL)
            ->filterByFkBranch($driverTransfer->getFkBranch())
            ->joinWithSpyBranch()
            ->joinWithSpyConcreteTimeSlot()
            ->useSpyConcreteTimeSlotQuery(null, Criteria::INNER_JOIN)
                ->leftJoinWithSpySalesOrder()
                ->useSpySalesOrderQuery()
                    ->leftJoinWith('SpySalesOrder.Order') // SpySalesOrder.SpySalesPayment is wrongly named SpySalesOrder.Order
                    ->useOrderQuery(null, Criteria::LEFT_JOIN)
                        ->leftJoinWithSalesPaymentMethodType()
                        ->useSalesPaymentMethodTypeQuery(null, Criteria::LEFT_JOIN)
                        ->endUse()
                    ->endUse()
                    ->useShippingAddressQuery('shippingAddress', Criteria::LEFT_JOIN)
                    ->endUse()
                    ->with('shippingAddress')
                    ->useBillingAddressQuery('billingAddress', Criteria::LEFT_JOIN)
                    ->endUse()
                    ->with('billingAddress')
                    ->leftJoinWithItem()
                    ->useItemQuery(null, Criteria::LEFT_JOIN)
                        ->filterByFkOmsOrderProcess($processIdWhiteList, Criteria::IN)
                        ->_or()
                        ->filterByFkOmsOrderProcess(null, Criteria::ISNULL)
                        ->filterByFkOmsOrderItemState($stateIdWhiteList, Criteria::IN)
                        ->_or()
                        ->filterByFkOmsOrderItemState(null, Criteria::ISNULL)
                    ->endUse()
                    ->leftJoinWithOrderComment()
                    ->useOrderCommentQuery(null, Criteria::LEFT_JOIN)
                    ->endUse()
                ->endUse()
                ->orderByStartTime(Criteria::DESC)
            ->endUse();
    }

    /**
     * @param array $skus
     *
     * @return SpyProductQuery
     */
    public function queryProductsWithAttributesBySkus(array $skus): SpyProductQuery
    {
        return $this
            ->getFactory()
            ->getProductQueryContainer()
            ->queryProduct()
            ->filterBySku_In($skus)
            ->leftJoinWithSpyProductLocalizedAttributes()
            ->useSpyProductLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
            ->endUse();
    }

    /**
     * @param int $idVehicleType
     * @return SpyConcreteTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryFutureConcreteTimeSlotsWithVehicleType(int $idVehicleType) : SpyConcreteTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotQuery()
            ->useDstConcreteTourQuery()
                ->useDstAbstractTourQuery()
                    ->filterByFkVehicleType($idVehicleType)
                ->endUse()
            ->endUse()
            ->filterByStartTime(['min' => 'now'], Criteria::GREATER_EQUAL);
    }

    /**
     * @param int $idAbstractTour
     * @return SpyConcreteTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryFutureConcreteTimeSlotsByAbstractTourId(int $idAbstractTour) : SpyConcreteTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotQuery()
            ->useDstConcreteTourQuery()
                ->filterByFkAbstractTour($idAbstractTour)
            ->endUse()
            ->filterByStartTime(['min' => 'now'], Criteria::GREATER_EQUAL);
    }

    /**
     * @param int $fkBranch
     * @param array $hiddenStateList
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param string|null $status
     * @return DstConcreteTourQuery
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function queryConcreteToursForPagination(
        int $fkBranch,
        array $hiddenStateList,
        DateTime $startDate = null,
        DateTime $endDate = null,
        string $status = null
    ): DstConcreteTourQuery {
        $query = $this
            ->getFactory()
            ->createConcreteTourQuery()
            ->select([DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR])
            ->filterByFkBranch($fkBranch);

        $config = $this->getFactory()->getConfig();

        $earliestAllowedDate = new DateTime($config->getConcreteTourFilteringEarliestAllowedDate());

        $query->filterByDate(
            ($startDate !== null && $startDate >= $earliestAllowedDate)
                ? $startDate
                : $earliestAllowedDate,
            Criteria::GREATER_EQUAL
        );

        if ($endDate !== null) {
            $query->filterByDate($endDate, Criteria::LESS_EQUAL);
        }

        $query = $query->useStateQuery();

        if ($status !== null) {
            $query->filterByName(
                $config->getConcreteTourStatusMap()[$status],
                Criteria::IN
            );
        } else {
            $query->filterByName(
                $hiddenStateList,
                Criteria::NOT_IN
            );
        }

        $query =
            $query
                    ->_or()
                    ->filterByName(null, Criteria::ISNULL)
                ->endUse();

        $query
            ->useSpyConcreteTimeSlotQuery(null, Criteria::INNER_JOIN)
                ->addAsColumn(
                    ConcreteTour::MIN_CONCRETE_TIME_SLOT_START_COLUMN_NAME,
                    sprintf('MIN(%s)', SpyConcreteTimeSlotTableMap::COL_START_TIME)
                )
                ->addAsColumn(
                    ConcreteTour::MAX_CONCRETE_TIME_SLOT_END_COLUMN_NAME,
                    sprintf('MAX(%s)', SpyConcreteTimeSlotTableMap::COL_END_TIME)
                )
            ->endUse()
            ->groupByIdConcreteTour();

        switch ($status) {
            case TourConstants::CONCRETE_TOUR_STATUS_DELIVERED:
                $query->orderByDate(Criteria::DESC);
                break;
            case null:
            default:
                $query->orderByDate();
                break;
        }

        $query->orderBy(ConcreteTour::MIN_CONCRETE_TIME_SLOT_START_COLUMN_NAME)
            ->orderBy(ConcreteTour::MAX_CONCRETE_TIME_SLOT_END_COLUMN_NAME);

        return $query;
    }

    /**
     * @param array $idsConcreteTour
     * @param string|null $status
     * @return DstConcreteTourQuery
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function queryConcreteToursForIndex(array $idsConcreteTour, string $status = null): DstConcreteTourQuery
    {
        $config = $this
            ->getFactory()
            ->getConfig();

        $query = $this
            ->getFactory()
            ->createConcreteTourQuery()
            ->select([
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR,
                DstConcreteTourTableMap::COL_TOUR_REFERENCE,
                DstConcreteTourTableMap::COL_DATE,
                DstConcreteTourTableMap::COL_IS_COMMISSIONED,
                DstConcreteTourTableMap::COL_FK_BRANCH,
                DstConcreteTourTableMap::COL_FK_DRIVER,
                SpyStateMachineItemStateTableMap::COL_NAME,
                DstAbstractTourTableMap::COL_ID_ABSTRACT_TOUR,
                DstAbstractTourTableMap::COL_NAME,
                DstAbstractTourTableMap::COL_WEEKDAY,
                DstAbstractTourTableMap::COL_TIME_LOADING,
                DstAbstractTourTableMap::COL_TIME_APPROACH,
                DstVehicleTypeTableMap::COL_NAME,
                DstVehicleTypeTableMap::COL_PAYLOAD_KG,
            ])
            ->filterByIdConcreteTour($idsConcreteTour, Criteria::IN)
            ->leftJoinState(SpyStateMachineItemStateTableMap::TABLE_NAME)
            ->useDstAbstractTourQuery()
                ->useDstAbstractTourToAbstractTimeSlotQuery()
                    ->useSpyTimeSlotQuery()
                        ->addAsColumn(
                            ConcreteTour::MIN_TIME_SLOT_START_COLUMN_NAME,
                            sprintf('MIN(%s)', SpyTimeSlotTableMap::COL_START_TIME)
                        )
                        ->addAsColumn(
                            ConcreteTour::MAX_TIME_SLOT_END_COLUMN_NAME,
                            sprintf('MAX(%s)', SpyTimeSlotTableMap::COL_END_TIME)
                        )
                        ->addAsColumn(
                            ConcreteTour::AGG_TIME_SLOT_PREP_TIME_COLUMN_NAME,
                            sprintf(
                                'JSON_AGG(DISTINCT jsonb_build_object(\'id_time_slot\', %s, \'prep_time\', %s))',
                                SpyTimeSlotTableMap::COL_ID_TIME_SLOT,
                                SpyTimeSlotTableMap::COL_PREP_TIME
                            )
                        )
                    ->endUse()
                ->endUse()
                ->leftJoinDstVehicleType()
            ->endUse()
            ->useSpyConcreteTimeSlotQuery(null, Criteria::INNER_JOIN)
                ->useSpySalesOrderQuery(null, Criteria::LEFT_JOIN)
                    ->addAsColumn(
                        ConcreteTour::COUNT_SALES_ORDER_COLUMN_NAME,
                        sprintf('COUNT(DISTINCT %s)', SpySalesOrderTableMap::COL_ID_SALES_ORDER)
                    )
                    ->useOrderTotalQuery(null, Criteria::LEFT_JOIN)
                        ->addAsColumn(
                            ConcreteTour::AGG_SALES_ORDER_TOTAL_WEIGHT_COLUMN_NAME,
                            sprintf(
                                'JSON_AGG(DISTINCT jsonb_build_object(\'fk_sales_order\', %s, \'weight_total\', %s))',
                                SpySalesOrderTotalsTableMap::COL_FK_SALES_ORDER,
                                SpySalesOrderTotalsTableMap::COL_WEIGHT_TOTAL
                            )
                        )
                    ->endUse()
                    ->useItemQuery(null, Criteria::LEFT_JOIN)
                        ->useProcessQuery()
                            ->filterByName($config->getActiveProcesses(), Criteria::IN)
                            ->_or()
                            ->filterByName(null, Criteria::ISNULL)
                        ->endUse()
                        ->useStateQuery(null, Criteria::LEFT_JOIN)
                            ->filterByName($config->getStateBlacklist(), Criteria::NOT_IN)
                            ->_or()
                            ->filterByName(null, Criteria::ISNULL)
                        ->endUse()
                    ->endUse()
                    ->useShippingAddressQuery(null, Criteria::LEFT_JOIN)
                        ->addAsColumn(
                            ConcreteTour::AGG_DELIVERY_AREA_ZIP_CODE_COLUMN_NAME,
                            sprintf('JSON_AGG(DISTINCT %s)', SpySalesOrderAddressTableMap::COL_ZIP_CODE)
                        )
                    ->endUse()
                ->endUse()
            ->endUse()
            ->groupByIdConcreteTour();

        switch ($status) {
            case TourConstants::CONCRETE_TOUR_STATUS_DELIVERED:
                $query->orderByDate(Criteria::DESC);
                break;
            case null:
            default:
                $query->orderByDate();
                break;
        }

        $query->orderBy(ConcreteTour::MIN_TIME_SLOT_START_COLUMN_NAME)
            ->orderBy(ConcreteTour::MAX_TIME_SLOT_END_COLUMN_NAME);

        return $query;
    }

    /**
     * @param int $fkBranch
     * @return DstConcreteTourQuery
     * @throws AmbiguousComparisonException
     */
    public function queryConcreteToursByFkBranch(int $fkBranch): DstConcreteTourQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTourQuery()
            ->filterByFkBranch($fkBranch);
    }
}
