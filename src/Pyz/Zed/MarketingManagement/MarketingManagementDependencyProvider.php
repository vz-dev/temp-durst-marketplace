<?php

namespace Pyz\Zed\MarketingManagement;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class MarketingManagementDependencyProvider extends AbstractBundleDependencyProvider
{
    public const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';
    public const FACADE_MONEY = 'FACADE_MONEY';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addDiscountQueryContainer($container);
        $container = $this->addMoneyFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDiscountQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->discount()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container) : Container
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return $container->getLocator()->money()->facade();
        };

        return $container;
    }
}
