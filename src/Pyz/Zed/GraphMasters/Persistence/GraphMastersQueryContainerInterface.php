<?php

namespace Pyz\Zed\GraphMasters\Persistence;

use DateTime;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersCommissioningTimeQuery;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTimeQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrderQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersSettingsQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTimeSlotQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTourQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface GraphMastersQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return DstGraphmastersSettingsQuery
     */
    public function queryGraphmastersSettings(): DstGraphmastersSettingsQuery;

    /**
     * @param int $idBranch
     * @return DstGraphmastersSettingsQuery
     */
    public function queryGraphMastersSettingsByIdBranch(int $idBranch): DstGraphmastersSettingsQuery;

    /**
     * @param int $idSettings
     * @return DstGraphmastersSettingsQuery
     */
    public function queryGraphMastersSettingsById(int $idSettings) : DstGraphmastersSettingsQuery;

    /**
     * @return DstGraphmastersDeliveryAreaCategoryQuery
     */
    public function queryGraphmastersDeliveryAreaCategory() : DstGraphmastersDeliveryAreaCategoryQuery;

    /**
     * @return DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery
     */
    public function queryCategoryToDeliveryArea() : DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery;

    /**
     * @param int $idCategory
     * @return DstGraphmastersDeliveryAreaCategoryQuery
     */
    public function queryGraphMastersCategoryById(int $idCategory) : DstGraphmastersDeliveryAreaCategoryQuery;

    /**
     * @return DstGraphmastersTimeSlotQuery
     */
    public function createGraphmastersTimeSlotQuery() : DstGraphmastersTimeSlotQuery;

    /**
     * @return DstGraphmastersOpeningTimeQuery
     */
    public function createGraphmastersOpeningTimeQuery(): DstGraphmastersOpeningTimeQuery;

    /**
     * @return DstGraphmastersTourQuery
     */
    public function createGraphmastersTourQuery(): DstGraphmastersTourQuery;

    /**
     * @param int $fkBranch
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param string|null $virtualStatus
     * @return DstGraphmastersTourQuery
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function queryToursForPagination(
        int $fkBranch,
        DateTime $startDate = null,
        DateTime $endDate = null,
        string $virtualStatus = null
    ): DstGraphmastersTourQuery;

    /**
     * @param array $idTours
     * @param string|null $virtualStatus
     * @return DstGraphmastersTourQuery
     */
    public function queryToursForIndex(array $idTours, string $virtualStatus = null): DstGraphmastersTourQuery;

    /**
     * @return DstGraphmastersCommissioningTimeQuery
     */
    public function createGraphmastersCommissioningTimeQuery(): DstGraphmastersCommissioningTimeQuery;

    /**
     * @return DstGraphmastersOrderQuery
     */
    public function createGraphmastersOrderQuery(): DstGraphmastersOrderQuery;

    /**
     * @param DriverTransfer $driverTransfer
     * @param array $processIdWhiteList
     * @param array $stateIdWhiteList
     * @param DateTime $newerThan
     * @param DateTime $olderThan
     * @return DstGraphmastersTourQuery
     */
    public function queryToursHydratedForDriverApp(
        DriverTransfer $driverTransfer,
        array $processIdWhiteList,
        array $stateIdWhiteList,
        DateTime $newerThan,
        DateTime $olderThan
    ): DstGraphmastersTourQuery;

    /**
     * @param int $fkBranch
     * @return DstGraphmastersTourQuery
     * @throws AmbiguousComparisonException
     */
    public function queryTodaysIdleToursByFkBranch(int $fkBranch): DstGraphmastersTourQuery;
}
