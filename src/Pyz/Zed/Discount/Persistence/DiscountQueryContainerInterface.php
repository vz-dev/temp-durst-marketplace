<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:38
 */

namespace Pyz\Zed\Discount\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\Sales\Persistence\SpySalesDiscountQuery;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Discount\Persistence\DiscountQueryContainerInterface as SprykerDiscountQueryContainerInterface;

interface DiscountQueryContainerInterface extends SprykerDiscountQueryContainerInterface
{
    /**
     * @param int $idBranch
     * @return SpyDiscountQuery
     */
    public function getDiscountsByIdBranch(int $idBranch): SpyDiscountQuery;

    /**
     * @param int $idBranch
     * @return SpyDiscountQuery
     */
    public function queryActiveAndRunningDiscountsByIdBranch(int $idBranch): SpyDiscountQuery;

    /**
     * @param int $idBranch
     * @param string|null $sku
     * @return SpyDiscountQuery
     */
    public function queryActiveAndRunningDiscountsByIdBranchAndSku(int $idBranch, string $sku = null): SpyDiscountQuery;

    /**
     * @param int[] $idOrders
     * @return SpySalesDiscountQuery
     */
    public function queryVoucherDiscountsByOrderIds(array $idOrders): SpySalesDiscountQuery;

    /**
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function queryCartDiscountGroup(): DstCartDiscountGroupQuery;

    /**
     * @param int $idCartDiscountGroup
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function queryCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): DstCartDiscountGroupQuery;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function queryCartDiscountGroupByBranch(
        int $idBranch
    ): DstCartDiscountGroupQuery;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function queryAllCartDiscountGroupsByBranch(
        int $idBranch
    ): DstCartDiscountGroupQuery;

    /**
     * @param string $scope
     * @return \Propel\Runtime\Collection\ObjectCollection|array
     */
    public function queryCartDiscountGroupList(
        string $scope
    ): ObjectCollection;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     */
    public function queryCampaignPeriodBranchOrderProductByBranch(
        int $idBranch
    ): DstCampaignPeriodBranchOrderProductQuery;
}
