<?php

namespace Pyz\Zed\MerchantPrice\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderItemTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method MerchantPricePersistenceFactory getFactory()
 */
class MerchantPriceQueryContainer extends AbstractQueryContainer implements MerchantPriceQueryContainerInterface
{
    public const RELATION_PRODUCT_ABSTRACT_LOCALIZED_ATTRIBUTES = 'productAbstractLocalizedAttributes';

    /**
     * {@inheritdoc}
     */
    public function queryPrice()
    {
        return $this->getFactory()->createPriceQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryCampaigns()
    {
        return $this->getFactory()->createCampaignQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryCampaignPeriodBranchOrders()
    {
        return $this->getFactory()->createCampaignPeriodBranchOrderQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryDiscounts()
    {
        return $this->getFactory()->createDiscountQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function querySalesOrdersByIdBranchAndIdProduct(
        int $idBranch,
        int $idProduct,
        string $configPeriod
    ): int
    {
        $date = date('Y-m-d h:m:s', strtotime($configPeriod));
        $todayDate = date('Y-m-d h:m:s');
        $product = $this->getFactory()
            ->createProductQuery()
            ->findOneByIdProduct($idProduct);

        return $this->getFactory()
            ->createSalesOrderQuery()
            ->useItemQuery()
                ->addJoin(SpySalesOrderItemTableMap::COL_SKU, SpyProductTableMap::COL_SKU)
                ->filterBySku($product->getSku())
            ->endUse()
            ->filterByFkBranch($idBranch)
            ->filterByCreatedAt_Between(['min' => $date, 'max' => $todayDate])
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function queryPriceByIdBranchAndIdProduct($idBranch, $idProduct)
    {
        return $this
            ->getFactory()
            ->createPriceQuery()
            ->filterByFkBranch($idBranch)
            ->filterByFkProduct($idProduct);
    }

    /**
     * {@inheritdoc}
     */
    public function queryPricesByIdBranch($idBranch)
    {
        return $this
            ->getFactory()
            ->createPriceQuery()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     */
    public function queryPriceBySku($sku)
    {
        return $this
            ->getFactory()
            ->createPriceQuery()
            ->filterBySku($sku);
    }

    /**
     * {@inheritdoc}
     */
    public function queryPrices()
    {
        return $this->getFactory()->createPriceQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryProducts()
    {
        return $this->getFactory()->createProductQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryArchivePrices()
    {
        return $this->getFactory()->createArchivePriceQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function queryPriceById($idPrice)
    {
        return $this
            ->getFactory()
            ->createPriceQuery()
            ->filterByIdPrice($idPrice);
    }

    /**
     * @param int $idBranch
     * @param string $concreteSku
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryActivePricesByIdBranchAndConcreteSku(int $idBranch, string $concreteSku): MerchantPriceQuery
    {
        return $this
            ->getFactory()
            ->createPriceQuery()
            ->filterByFkBranch($idBranch)
            ->useSpyProductQuery()
                ->filterByIsActive(true)
                ->filterBySku($concreteSku)
            ->endUse()
            ->filterByStatus('active')
            ->_or()
            ->filterByStatus('out_of_stock');
    }

    /**
     * {@inheritDoc}
     *
     * @param array $branchIds
     * @param int $idLocale
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryActivePricesAndProductsForBranches(array $branchIds, int $idLocale): MerchantPriceQuery
    {
        return $this
            ->queryPrices()
            ->filterByFkBranch_In($branchIds)
            ->filterByStatus('active')
            ->_or()
            ->filterByStatus('out_of_stock')
            ->joinWithSpyBranch()
            ->useSpyBranchQuery()
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->endUse()
            ->joinWithSpyProduct()
            ->useSpyProductQuery()
                ->filterByIsActive(true)
                ->orderBySku()
                ->joinWithSpyDeposit(Criteria::LEFT_JOIN)
                ->useSpyDepositQuery()
                    ->orderByName()
                ->endUse()
                ->joinWithSpyProductAbstract()
                ->useSpyProductAbstractQuery()
                    ->joinWithSpyProductCategory()
                    ->useSpyProductCategoryQuery()
                        ->joinWithSpyCategory()
                        ->useSpyCategoryQuery()
                            ->orderByIdCategory()
                            ->filterByIsActive(true)
                            ->joinWithAttribute()
                            ->useAttributeQuery()
                                ->filterByFkLocale($idLocale)
                            ->endUse()
                        ->endUse()
                    ->endUse()
                    ->joinWithSpyManufacturer(Criteria::LEFT_JOIN)
                    ->useSpyManufacturerQuery()
                        ->orderByName()
                    ->endUse()
                    ->joinWithSpyProductAbstractLocalizedAttributes(Criteria::LEFT_JOIN)
                ->endUse()
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @throws AmbiguousComparisonException
     */
    public function queryActivePricesAndDepositsForProductByIdBranchAndSku(int $idBranch, string $sku, bool $deactivated = false): MerchantPriceQuery
    {
        $query = $this
            ->queryPrices()
            ->filterByFkBranch($idBranch);

        if (!$deactivated) {
            $query->filterByStatus('active')
                ->_or()
                ->filterByStatus('out_of_stock');
        }

        $query->joinWithSpyBranch()
            ->useSpyBranchQuery()
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->endUse()
            ->joinWithSpyProduct()
            ->useSpyProductQuery()
                ->filterByIsActive(true)
                ->joinWithSpyDeposit(Criteria::LEFT_JOIN)
                ->useSpyDepositQuery()
                    ->orderByName()
                ->endUse()
                ->joinWithSpyProductAbstract()
                ->useSpyProductAbstractQuery()
                    ->filterBySku($sku)
                    ->joinWithSpyManufacturer(Criteria::LEFT_JOIN)
                    ->useSpyManufacturerQuery()
                        ->orderByName()
                    ->endUse()
                    ->joinWithSpyProductAbstractLocalizedAttributes(Criteria::LEFT_JOIN)
                ->endUse()
            ->endUse();

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @throws AmbiguousComparisonException
     */
    public function queryProductWithActiveCampaign(string $sku, int $branchId): DstCampaignPeriodQuery
    {
        $query = $this
            ->queryCampaigns()
            ->useDstCampaignPeriodBranchOrderQuery()
                ->useDstCampaignPeriodBranchOrderProductQuery()
                    ->filterBySku($sku)
                ->endUse()
                ->filterByFkBranch($branchId)
            ->endUse()
            ->filterByIsActive(true)
            ->filterByCampaignEndDate(date("Y-m-d H:i:s"), Criteria::GREATER_EQUAL);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @throws AmbiguousComparisonException
     */
    public function queryProductWithActiveDiscount(string $sku, int $branchId): SpyDiscountQuery
    {
        return $this
            ->queryDiscounts()
            ->filterByDiscountSku($sku)
            ->filterByIsActive(true)
            ->filterByFkBranch($branchId)
            ->filterByValidTo(date("Y-m-d H:i:s"), Criteria::GREATER_EQUAL);
    }
}
