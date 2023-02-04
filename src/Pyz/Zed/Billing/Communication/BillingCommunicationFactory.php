<?php

namespace Pyz\Zed\Billing\Communication;

use Pyz\Zed\Billing\Communication\Table\BillingItemTable;
use Pyz\Zed\Billing\Communication\Table\BillingPeriodTable;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \Pyz\Zed\Billing\Persistence\BillingQueryContainer getQueryContainer()
 * @method \Pyz\Zed\Billing\BillingConfig getConfig()
 */
class BillingCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return BillingPeriodTable
     */
    public function createBillingPeriodTable(): BillingPeriodTable
    {
        return new BillingPeriodTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return BillingItemTable
     */
    public function createBillingItemTable(): BillingItemTable
    {
        return new BillingItemTable(
            $this->getQueryContainer()
        );
    }
}
