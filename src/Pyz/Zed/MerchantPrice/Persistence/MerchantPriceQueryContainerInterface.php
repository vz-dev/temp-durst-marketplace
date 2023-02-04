<?php

namespace Pyz\Zed\MerchantPrice\Persistence;

use Orm\Zed\Campaign\Persistence\Base\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;

interface MerchantPriceQueryContainerInterface
{
    /**
     * Returns a price query filtered by combined primary key fk_branch
     * and fk_product
     *
     * @param int $idBranch
     * @param int $idProduct
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPriceByIdBranchAndIdProduct($idBranch, $idProduct);

    /**
     * Returns a price query filtered by the internal sku. This field
     * unique, so it only ever returns one result
     *
     * @param string $sku
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPriceBySku($sku);

    /**
     * Returns a query to find all prices for a given branch.
     *
     * @param int $idBranch
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPricesByIdBranch($idBranch);

    /**
     * Returns a price query filtered by its primary key.
     *
     * @param int $idPrice
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPriceById($idPrice);

    /**
     * Returns a plain price query without filters
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPrices();

    /**
     * Returns a plain price query without filters
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceArchiveQuery
     */
    public function queryArchivePrices();

    /**
     * Returns a plain price query without filters
     *
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery
     */
    public function queryCampaigns();

    /**
     * Returns a plain price query without filters
     *
     * @return \Orm\Zed\Product\Persistence\SpyProduct
     */
    public function queryProducts();

    /**
     * Returns a plain order sales query for the specific branch
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return int
     */
    public function querySalesOrdersByIdBranchAndIdProduct(int $idBranch, int $idProduct, string $configPeriod): int;

    /**
     * @param int $idBranch
     * @param string $concreteSku
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryActivePricesByIdBranchAndConcreteSku(int $idBranch, string $concreteSku): MerchantPriceQuery;

    /**
     * Retrieves the prices and deposits for the product identified by the given branch ID and SKU.
     *
     * @param int $idBranch
     * @param string $sku
     * @param bool $deactivated
     *
     * @return MerchantPriceQuery
     */
    public function queryActivePricesAndDepositsForProductByIdBranchAndSku(int $idBranch, string $sku, bool $deactivated): MerchantPriceQuery;

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function queryProductWithActiveCampaign(string $sku, int $idBranch): DstCampaignPeriodQuery;

    /**
     * @param string $sku
     * @param int $branchId
     *
     * @return bool
     */
    public function queryProductWithActiveDiscount(string $sku, int $branchId): SpyDiscountQuery;
}
