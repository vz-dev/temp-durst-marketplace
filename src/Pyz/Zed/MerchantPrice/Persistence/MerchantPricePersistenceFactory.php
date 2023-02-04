<?php

namespace Pyz\Zed\MerchantPrice\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceArchiveQuery;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\MerchantPrice\MerchantPriceConfig getConfig()
 * @method \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainer getQueryContainer()
 */
class MerchantPricePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return MerchantPriceQuery
     */
    public function createPriceQuery()
    {
        return MerchantPriceQuery::create();
    }

    /**
     * @return MerchantPriceArchiveQuery
     */
    public function createArchivePriceQuery()
    {
        return MerchantPriceArchiveQuery::create();
    }

    /**
     * @return SpySalesOrderQuery
     */
    public function createSalesOrderQuery()
    {
        return SpySalesOrderQuery::create();
    }

    /**
     * @return SpyProductQuery
     */
    public function createProductQuery()
    {
        return SpyProductQuery::create();
    }

    /**
     * @return DstCampaignPeriodQuery
     */
    public function createCampaignQuery()
    {
        return DstCampaignPeriodQuery::create();
    }

    /**
     * @return DstCampaignPeriodBranchOrderQuery
     */
    public function createCampaignPeriodBranchOrderQuery()
    {
        return DstCampaignPeriodBranchOrderQuery::create();
    }

    /**
     * @return SpyDiscountQuery
     */
    public function createDiscountQuery()
    {
        return SpyDiscountQuery::create();
    }
}
