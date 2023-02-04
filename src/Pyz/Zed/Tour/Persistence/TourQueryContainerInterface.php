<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 10:37
 */

namespace Pyz\Zed\Tour\Persistence;

use DateTime;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemStateQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcessQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourExportQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery;
use Orm\Zed\Tour\Persistence\DstVehicleCategoryQuery;
use Orm\Zed\Tour\Persistence\DstVehicleQuery;
use Orm\Zed\Tour\Persistence\DstVehicleTypeQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface TourQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return DstVehicleTypeQuery
     */
    public function queryVehicleType() : DstVehicleTypeQuery;

    /**
     * @param int $idVehicleType
     *
     * @return DstVehicleTypeQuery
     */
    public function queryVehicleTypeById(int $idVehicleType) : DstVehicleTypeQuery;

    /**
     * @return DstAbstractTourQuery
     */
    public function queryAbstractTour() : DstAbstractTourQuery;

    /**
     * @return DstConcreteTourQuery
     */
    public function queryConcreteTour() : DstConcreteTourQuery;

    /**
     * @return DstVehicleQuery
     */
    public function queryVehicle() : DstVehicleQuery;

    /**
     * @param int $idVehicle
     *
     * @return DstVehicleQuery
     */
    public function queryVehicleById(int $idVehicle) : DstVehicleQuery;

    /**
     * @return DstVehicleQuery
     */
    public function queryVehicleActive() : DstVehicleQuery;

    /**
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicence() : DstDrivingLicenceQuery;

    /**
     * @param int $idDrivingLicence
     *
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicenceById(int $idDrivingLicence) : DstDrivingLicenceQuery;

    /**
     * @param string $code
     *
     * @return DstDrivingLicenceQuery
     */
    public function queryDrivingLicenceByCode(string $code) : DstDrivingLicenceQuery;

    /**
     * @param int $idAbstractTour
     *
     * @return DstAbstractTourQuery
     */
    public function queryAbstractTourById(int $idAbstractTour) : DstAbstractTourQuery;

    /**
     * @param int $idConcreteTour
     *
     * @return DstConcreteTourQuery
     */
    public function queryConcreteTourById(int $idConcreteTour) : DstConcreteTourQuery;

    /**
     * @return DstAbstractTourToAbstractTimeSlotQuery
     */
    public function queryAbstractTourToAbstractTimeSlot() : DstAbstractTourToAbstractTimeSlotQuery;

    /**
     * @param array $orderItemStates
     *
     * @return SpyOmsOrderItemStateQuery
     */
    public function querySalesOrderItemStatesByName(array $orderItemStates) : SpyOmsOrderItemStateQuery;

    /**
     * @param array $processes
     *
     * @return SpyOmsOrderProcessQuery
     */
    public function querySalesOrderProcessesByName(array $processes) : SpyOmsOrderProcessQuery;

    /**
     * @return DstConcreteTourExportQuery
     */
    public function queryConcreteTourExport(): DstConcreteTourExportQuery;

    /**
     * @param int $idConcreteTourExport
     *
     * @return DstConcreteTourExportQuery
     */
    public function queryConcreteTourExportById(int $idConcreteTourExport): DstConcreteTourExportQuery;

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     *
     * @return DstConcreteTourQuery
     */
    public function querySimultaneousConcreteToursByConcreteTour(ConcreteTourTransfer $concreteTourTransfer): DstConcreteTourQuery;

    /**
     * @param int[] $stateIds
     *
     * @return DstConcreteTourQuery
     */
    public function queryStateMachineItemsByStateIds(array $stateIds = []): DstConcreteTourQuery;

    /**
     * @param int $idOrder
     *
     * @return DstConcreteTourQuery
     *@throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     *
     */
    public function queryConcreteTourByIdOrder(int $idOrder): DstConcreteTourQuery;

    /**
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
    ): DstConcreteTourQuery;

    /**
     * @param array $skus
     *
     * @return SpyProductQuery
     */
    public function queryProductsWithAttributesBySkus(array $skus): SpyProductQuery;

    /**
     * @param int $idVehicleType
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryFutureConcreteTimeSlotsWithVehicleType(int $idVehicleType) : SpyConcreteTimeSlotQuery;

    /**
     * @param int $idAbstractTour
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryFutureConcreteTimeSlotsByAbstractTourId(int $idAbstractTour) : SpyConcreteTimeSlotQuery;

    /**
     * @param int $fkBranch
     * @param array $hiddenStateList
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param string|null $status
     *
     * @return DstConcreteTourQuery
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function queryConcreteToursForPagination(
        int $fkBranch,
        array $hiddenStateList,
        DateTime $startDate = null,
        DateTime $endDate = null,
        string $status = null
    ): DstConcreteTourQuery;

    /**
     * @param array $idsConcreteTour
     * @param string|null $status
     *
     * @return DstConcreteTourQuery
     *
     * @throws AmbiguousComparisonException
     */
    public function queryConcreteToursForIndex(array $idsConcreteTour, string $status = null): DstConcreteTourQuery;

    /**
     * @param int $fkBranch
     *
     * @return DstConcreteTourQuery
     *
     * @throws AmbiguousComparisonException
     */
    public function queryConcreteToursByFkBranch(int $fkBranch): DstConcreteTourQuery;

    /**
     * @return DstVehicleCategoryQuery
     *
     * @throws AmbiguousComparisonException
     */
    public function queryVehicleCategoryActive(): DstVehicleCategoryQuery;
}
