<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:40
 */

namespace Pyz\Zed\Discount\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\Sales\Persistence\SpySalesDiscountCodeQuery;
use Orm\Zed\Sales\Persistence\SpySalesDiscountQuery;
use Propel\Runtime\Collection\ObjectCollection;
use Pyz\Shared\Discount\DiscountConstants;
use Spryker\Zed\Discount\Persistence\DiscountQueryContainer as SprykerDiscountQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class DiscountQueryContainer
 * @package Pyz\Zed\Discount\Persistence
 * @method DiscountPersistenceFactory getFactory()
 */
class DiscountQueryContainer extends SprykerDiscountQueryContainer implements DiscountQueryContainerInterface
{
    /**
     * @param int $idBranch
     * @return SpyDiscountQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDiscountsByIdBranch(int $idBranch): SpyDiscountQuery
    {
        return $this
            ->getFactory()
            ->createDiscountQuery()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @param int $idBranch
     * @return SpyDiscountQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryActiveAndRunningDiscountsByIdBranch(int $idBranch): SpyDiscountQuery
    {
        $query = parent::queryActiveAndRunningDiscounts();

        $query
            ->filterByFkBranch($idBranch);

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AmbiguousComparisonException
     */
    public function queryActiveAndRunningDiscountsByIdBranchAndSku(int $idBranch, string $sku = null): SpyDiscountQuery
    {
        $query = parent::queryActiveAndRunningDiscounts();

        $query
            ->filterByFkBranch($idBranch);

        if ($sku !== null) {
            $query->filterByDiscountSku_Like($sku . '%');
        }

        return $query;
    }

    /**
     * @param int[] $idOrders
     * @return SpySalesDiscountQuery
     * @throws AmbiguousComparisonException
     */
    public function queryVoucherDiscountsByOrderIds(array $idOrders): SpySalesDiscountQuery
    {
        return $this
            ->getFactory()
            ->createSalesDiscountQuery()
            ->filterByFkSalesOrder_In($idOrders)
            ->joinWithSpyDiscount()
            ->useSpyDiscountQuery()
                ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER)
            ->endUse()
            ->joinWithExpense();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function queryCartDiscountGroup(): DstCartDiscountGroupQuery
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCartDiscountGroup
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): DstCartDiscountGroupQuery
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupQuery()
            ->filterByIsMainDiscount(true)
            ->filterByIdCartDiscountGroup($idCartDiscountGroup)
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryCartDiscountGroupByBranch(int $idBranch): DstCartDiscountGroupQuery
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupQuery()
            ->filterByIsMainDiscount(true)
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Sales\Persistence\SpySalesDiscountCodeQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function querySalesDiscountCode(int $idSalesDiscount): SpySalesDiscountCodeQuery
    {
        return $this
            ->getFactory()
            ->createSalesDiscountCodeQuery()
            ->filterByFkSalesDiscount($idSalesDiscount);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryAllCartDiscountGroupsByBranch(int $idBranch): DstCartDiscountGroupQuery
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupQuery()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $scope
     * @return \Propel\Runtime\Collection\ObjectCollection|array
     */
    public function queryCartDiscountGroupList(string $scope): ObjectCollection
    {
        return DstCartDiscountGroupQuery::retrieveList($scope);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryCampaignPeriodBranchOrderProductByBranch(
        int $idBranch
    ): DstCampaignPeriodBranchOrderProductQuery
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderProductQuery()
            ->useDstCampaignPeriodBranchOrderQuery()
                ->filterByFkBranch(
                    $idBranch
                )
            ->endUse();
    }
}
