<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:38
 */

namespace Pyz\Zed\Discount\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery;
use Orm\Zed\Discount\Persistence\SpyDiscountQuery;
use Orm\Zed\Sales\Persistence\SpySalesDiscountCodeQuery;
use Orm\Zed\Sales\Persistence\SpySalesDiscountQuery;
use Spryker\Zed\Discount\Persistence\DiscountPersistenceFactory as SprykerDiscountPersistenceFactory;

class DiscountPersistenceFactory extends SprykerDiscountPersistenceFactory
{
    /**
     * @return SpyDiscountQuery
     */
    public function createDiscountQuery(): SpyDiscountQuery
    {
        return SpyDiscountQuery::create();
    }

    /**
     * @return SpySalesDiscountQuery
     */
    public function createSalesDiscountQuery(): SpySalesDiscountQuery
    {
        return SpySalesDiscountQuery::create();
    }

    /**
     * @return SpySalesDiscountCodeQuery
     */
    public function createSalesDiscountCodeQuery(): SpySalesDiscountCodeQuery
    {
        return SpySalesDiscountCodeQuery::create();
    }

    /**
     * @return \Orm\Zed\Discount\Persistence\DstCartDiscountGroupQuery
     */
    public function createCartDiscountGroupQuery(): DstCartDiscountGroupQuery
    {
        return DstCartDiscountGroupQuery::create();
    }

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery
     */
    public function createCampaignPeriodQuery(): DstCampaignPeriodQuery
    {
        return DstCampaignPeriodQuery::create();
    }

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     */
    public function createCampaignPeriodBranchOrderProductQuery(): DstCampaignPeriodBranchOrderProductQuery
    {
        return DstCampaignPeriodBranchOrderProductQuery::create();
    }
}
