<?php
/**
 * Durst - project - ProductExportDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:39
 */

namespace Pyz\Zed\ProductExport;


use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMailBridge;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMerchantPriceBridge;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToProductBridge;
use Pyz\Zed\ProductExport\Dependency\Persistence\ProductExportToProductQueryContainerBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class ProductExportDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MERCHANT_PRICE = 'FACADE_MERCHANT_PRICE';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';

    public const QUERY_CONTAINER_PRODUCT_EXPORT = 'QUERY_CONTAINER_PRODUCT_EXPORT';
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addMerchantPriceFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addMerchantFacade($container);

        $container = $this->addProductExportQueryContainer($container);
        $container = $this->addProductQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addProductExportQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT_PRICE] = function (Container $container) {
            return new ProductExportToMerchantPriceBridge(
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
            return new ProductExportToProductBridge(
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
            return new ProductExportToMailBridge(
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
    protected function addProductExportQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT_EXPORT] = function (Container $container) {
            return $container
                ->getLocator()
                ->productExport()
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
            return new ProductExportToProductQueryContainerBridge(
                $container
                    ->getLocator()
                    ->product()
                    ->queryContainer()
            );
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
