<?php

namespace Pyz\Zed\MarketingManagement\Communication;

use Pyz\Zed\Discount\Persistence\DiscountQueryContainer;
use Pyz\Zed\MarketingManagement\Communication\Table\DiscountTable;
use Pyz\Zed\MarketingManagement\Communication\Table\VoucherTable;
use Pyz\Zed\MarketingManagement\MarketingManagementDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

/**
 * @method \Pyz\Zed\MarketingManagement\Persistence\MarketingManagementQueryContainer getQueryContainer()
 * @method \Pyz\Zed\MarketingManagement\MarketingManagementConfig getConfig()
 */
class MarketingManagementCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\MarketingManagement\Communication\Table\DiscountTable
     */
    public function createDiscountTable() : DiscountTable
    {
        return new DiscountTable(
            $this->getDiscountQueryContainer(),
            $this->getConfig(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Persistence\DiscountQueryContainer
     */
    protected function getDiscountQueryContainer() : DiscountQueryContainer
    {
        return $this->getProvidedDependency(MarketingManagementDependencyProvider::QUERY_CONTAINER_DEPOSIT);
    }

    /**
     * @return \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected function getMoneyFacade() : MoneyFacadeInterface
    {
        return $this->getProvidedDependency(MarketingManagementDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \Pyz\Zed\MarketingManagement\Communication\Table\VoucherTable
     */
    public function createVoucherTable() : VoucherTable
    {
        return new VoucherTable(
            $this->getDiscountQueryContainer(),
            $this->getConfig(),
            $this->getMoneyFacade()
        );
    }
}
