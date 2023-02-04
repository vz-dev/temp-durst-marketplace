<?php
/**
 * Durst - project - CampaignDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 11:42
 */

namespace Pyz\Zed\Campaign;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CampaignDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_CAMPAIGN = 'FACADE_CAMPAIGN';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';
    public const FACADE_MERCHANT_PRICE = 'FACADE_MERCHANT_PRICE';
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_CURRENCY = 'FACADE_CURRENCY';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';

    public const PRODUCT_QUERY_CONTAINER = 'PRODUCT_QUERY_CONTAINER';
    public const DISCOUNT_QUERY_CONTAINER = 'DISCOUNT_QUERY_CONTAINER';

    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addCampaignFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addDiscountFacade($container);
        $container = $this->addMerchantPriceFacade($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addDepositFacade($container);
        $container = $this->addProductQueryContainer($container);
        $container = $this->addDiscountQueryContainer($container);
        $container = $this->addUtilEncodingService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCampaignFacade(Container $container): Container
    {
        $container[static::FACADE_CAMPAIGN] = function (Container $container) {
            return $container
                ->getLocator()
                ->campaign()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container): Container
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return $container
                ->getLocator()
                ->product()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDiscountFacade(Container $container): Container
    {
        $container[static::FACADE_DISCOUNT] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT_PRICE] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchantPrice()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container): Container
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return $container
                ->getLocator()
                ->money()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCurrencyFacade(Container $container): Container
    {
        $container[static::FACADE_CURRENCY] = function (Container $container) {
            return $container
                ->getLocator()
                ->currency()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDepositFacade(Container $container): Container
    {
        $container[static::FACADE_DEPOSIT] = function (Container $container) {
            return $container
                ->getLocator()
                ->deposit()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductQueryContainer(Container $container): Container
    {
        $container[static::PRODUCT_QUERY_CONTAINER] = function (Container $container) {
            return $container
                ->getLocator()
                ->product()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDiscountQueryContainer(Container $container): Container
    {
        $container[static::DISCOUNT_QUERY_CONTAINER] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container[static::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return $container
                ->getLocator()
                ->utilEncoding()
                ->service();
        };

        return $container;
    }
}
