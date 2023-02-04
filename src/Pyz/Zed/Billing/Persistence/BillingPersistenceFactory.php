<?php

namespace Pyz\Zed\Billing\Persistence;

use Orm\Zed\Billing\Persistence\DstBillingItemQuery;
use Orm\Zed\Billing\Persistence\DstBillingPeriodQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\Billing\BillingConfig getConfig()
 * @method \Pyz\Zed\Billing\Persistence\BillingQueryContainer getQueryContainer()
 */
class BillingPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstBillingPeriodQuery
     */
    public function createBillingPeriodQuery() : DstBillingPeriodQuery
    {
        return DstBillingPeriodQuery::create();
    }

    /**
     * @return DstBillingItemQuery
     */
    public function createBillingItemQuery() : DstBillingItemQuery
    {
        return DstBillingItemQuery::create();
    }
}
