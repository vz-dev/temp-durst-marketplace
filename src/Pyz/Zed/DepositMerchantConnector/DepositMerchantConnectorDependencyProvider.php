<?php
/**
 * Durst - project - DepositMerchantConnectorDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:25
 */

namespace Pyz\Zed\DepositMerchantConnector;

use Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridge;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridge;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class DepositMerchantConnectorDependencyProvider extends AbstractBundleDependencyProvider
{
    public const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';
    public const QUERY_CONTAINER_MERCHANT_PRICE = 'QUERY_CONTAINER_MERCHANT_PRICE';

    public const FACADE_LOCALE = 'FACADE_LOCALE';
    public const FACADE_TAX = 'FACADE_TAX';

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addDepositQueryContainer($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addMerchantPriceQueryContainer($container);
        $container = $this->addTaxFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDepositQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return new DepositMerchantConnectorToDepositBridge($container
                ->getLocator()
                ->deposit()
                ->queryContainer());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container): Container
    {
        $container[self::FACADE_LOCALE] = function (Container $container) {
            return new DepositMerchantConnectorToLocaleBridge($container
                ->getLocator()
                ->locale()
                ->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_MERCHANT_PRICE] = function (Container $container) {
            return new DepositMerchantConnectorToMerchantPriceBridge($container
                ->getLocator()
                ->merchantPrice()
                ->queryContainer());
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTaxFacade(Container $container): Container
    {
        $container[self::FACADE_TAX] = function (Container $container) {
            return $container->getLocator()->tax()->facade();
        };

        return $container;
    }
}
