<?php


namespace Pyz\Zed\MerchantPrice;

use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceSavePluginInterface;
use Pyz\Zed\Touch\Communication\Plugin\MerchantPrice\PostMerchantPriceDeletePlugin;
use Pyz\Zed\Touch\Communication\Plugin\MerchantPrice\PostMerchantPriceSavePlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class MerchantPriceDependencyProvider extends AbstractBundleDependencyProvider
{

    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_PRICE = 'FACADE_PRICE';
    public const FACADE_LOCALE = 'FACADE_LOCALE';
    public const FACADE_TAX = 'FACADE_TAX';

    public const POST_MERCHANT_PRICE_SAVE_PLUGINS = 'POST_MERCHANT_PRICE_SAVE_PLUGINS';
    public const POST_MERCHANT_PRICE_DELETE_PLUGINS = 'POST_MERCHANT_PRICE_DELETE_PLUGINS';

    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addProductFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addPriceFacade($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addTaxFacade($container);
        $container = $this->addPostMerchantPriceSavePlugins($container);
        $container = $this->addPostMerchantPriceDeletePlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addMerchantFacade(Container $container){
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addProductFacade(Container $container){
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addPriceFacade(Container $container){
        $container[static::FACADE_PRICE] = function (Container $container) {
            return $container->getLocator()->price()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addUtilEncodingService(Container $container)
    {
        $container[static::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return $container->getLocator()->utilEncoding()->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addLocaleFacade(Container $container)
    {
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return $container->getLocator()->locale()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTaxFacade(Container $container)
    {
        $container[static::FACADE_TAX] = function (Container $container) {
            return $container->getLocator()->tax()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addPostMerchantPriceSavePlugins(Container $container) : Container
    {
        $container[static::POST_MERCHANT_PRICE_SAVE_PLUGINS] = function (Container $container) {
            return $this->getPostMerchantPriceSavePlugins();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addPostMerchantPriceDeletePlugins(Container $container) : Container
    {
        $container[static::POST_MERCHANT_PRICE_DELETE_PLUGINS] = function (Container $container) {
            return $this->getPostMerchantPriceDeletePlugins();
        };

        return $container;
    }

    /**
     * @return PostMerchantPriceSavePluginInterface[]
     */
    protected function getPostMerchantPriceSavePlugins() : array
    {
        return [
            new PostMerchantPriceSavePlugin(),
        ];
    }

    /**
     * @return PostMerchantPriceDeletePluginInterface[]
     */
    protected function getPostMerchantPriceDeletePlugins() : array
    {
        return [
            new PostMerchantPriceDeletePlugin(),
        ];
    }
}
