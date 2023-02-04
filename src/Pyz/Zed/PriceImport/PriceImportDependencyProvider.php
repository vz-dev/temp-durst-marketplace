<?php
/**
 * Durst - project - PriceImportDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 10:56
 */

namespace Pyz\Zed\PriceImport;


use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridge;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridge;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class PriceImportDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MERCHANT_PRICE = 'FACADE_MERCHANT_PRICE';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';

    public const QUERY_CONTAINER_PRICE_IMPORT = 'QUERY_CONTAINER_PRICE_IMPORT';
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addMerchantPriceFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addMerchantFacade($container);

        $container = $this->addPriceImportQueryContainer($container);

        $container = $this->addProductQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT_PRICE] = function (Container $container) {
            return new PriceImportToMerchantPriceBridge(
                $container
                    ->getLocator()
                    ->merchantPrice()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container): Container
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return new PriceImportToProductBridge(
                $container
                    ->getLocator()
                    ->product()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMailFacade(Container $container): Container
    {
        $container[static::FACADE_MAIL] = function (Container $container) {
            return new PriceImportToMailBridge(
                $container
                    ->getLocator()
                    ->mail()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceImportQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRICE_IMPORT] = function (Container $container) {
            return $container
                ->getLocator()
                ->priceImport()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container
                ->getLocator()
                ->product()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->facade();
        };

        return $container;
    }
}
