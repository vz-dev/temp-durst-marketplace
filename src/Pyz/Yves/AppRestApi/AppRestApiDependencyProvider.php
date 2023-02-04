<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 11:23
 */

namespace Pyz\Yves\AppRestApi;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\Money\Plugin\MoneyPlugin;

class AppRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_APP_REST_API = 'CLIENT_APP_REST_API';
    public const CLIENT_CART = 'CLIENT_CART';
    public const CLIENT_CHECKOUT = 'CLIENT_CHECKOUT';
    public const CLIENT_SHIPMENT = 'CLIENT_SHIPMENT';
    public const CLIENT_PRICE = 'CLIENT_PRICE';
    public const CLIENT_DELIVERY_AREA = 'CLIENT_DELIVERY_AREA';
    public const CLIENT_TERMS_OF_SERVICE = 'CLIENT_TERMS_OF_SERVICE';
    public const CLIENT_MERCHANT = 'CLIENT_MERCHANT';
    public const CLIENT_SALES = 'CLIENT_SALES';
    public const CLIENT_HEIDELPAY_REST = 'CLIENT_HEIDELPAY_REST';
    public const CLIENT_DEPOSIT = 'CLIENT_DEPOSIT';
    public const CLIENT_GTIN = 'CLIENT_GTIN';
    public const CLIENT_TOUR = 'CLIENT_TOUR';
    public const CLIENT_OMS = 'CLIENT_OMS';
    public const CLIENT_AUTH = 'CLIENT_AUTH';
    public const CLIENT_DEPOSIT_MERCHANT_CONNECTOR = 'CLIENT_DEPOSIT_MERCHANT_CONNECTOR';
    public const CLIENT_DRIVER_APP = 'CLIENT_DRIVER_APP';
    public const CLIENT_DISCOUNT = 'CLIENT_DISCOUNT';
    public const CLIENT_CANCEL_ORDER = 'CLIENT_CANCEL_ORDER';

    public const PLUGIN_MONEY = 'PLUGIN_MONEY';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addAppRestApiClient($container);
        $container = $this->addCartClient($container);
        $container = $this->addCheckoutClient($container);
        $container = $this->addShipmentClient($container);
        $container = $this->addPriceClient($container);
        $container = $this->addDeliveryAreaClient($container);
        $container = $this->addTermsOfServiceClient($container);
        $container = $this->addMerchantClient($container);
        $container = $this->addSalesClient($container);
        $container = $this->addHeidelpayRestClient($container);
        $container = $this->addDepositClient($container);
        $container = $this->addGtinClient($container);
        $container = $this->addTourClient($container);
        $container = $this->addOmsClient($container);
        $container = $this->addAuthClient($container);
        $container = $this->addDepositMerchantConnectorClient($container);
        $container = $this->addDriverAppClient($container);
        $container = $this->addDiscountClient($container);
        $container = $this->addCancelOrderClient($container);

        $container = $this->providePlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function providePlugins(Container $container): Container
    {
        $container[self::PLUGIN_MONEY] = function () {
            return new MoneyPlugin();
        };
        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addAppRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_APP_REST_API] = function (Container $container) {
            return $container->getLocator()->appRestApi()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCartClient(Container $container): Container
    {
        $container[static::CLIENT_CART] = function (Container $container) {
            return $container->getLocator()->cart()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCheckoutClient(Container $container): Container
    {
        $container[static::CLIENT_CHECKOUT] = function (Container $container) {
            return $container->getLocator()->checkout()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShipmentClient(Container $container): Container
    {
        $container[static::CLIENT_SHIPMENT] = function (Container $container) {
            return $container->getLocator()->shipment()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPriceClient(Container $container): Container
    {
        $container[static::CLIENT_PRICE] = function (Container $container) {
            return $container->getLocator()->price()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addDeliveryAreaClient(Container $container): Container
    {
        $container[static::CLIENT_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addTermsOfServiceClient(Container $container): Container
    {
        $container[static::CLIENT_TERMS_OF_SERVICE] = function (Container $container) {
            return $container->getLocator()->termsOfService()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMerchantClient(Container $container): Container
    {
        $container[static::CLIENT_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addSalesClient(Container $container): Container
    {
        $container[static::CLIENT_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addHeidelpayRestClient(Container $container): Container
    {
        $container[static::CLIENT_HEIDELPAY_REST] = function (Container $container) {
            return $container->getLocator()->heidelpayRest()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addDepositClient(Container $container): Container
    {
        $container[static::CLIENT_DEPOSIT] = function (Container $container) {
            return $container
                ->getLocator()
                ->deposit()
                ->client();
        };

        return $container;
    }

    protected function addGtinClient(Container $container): Container
    {
        $container[static::CLIENT_GTIN] = function (Container $container) {
            return $container
                ->getLocator()
                ->productGtin()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addTourClient(Container $container): Container
    {
        $container[static::CLIENT_TOUR] = function (Container $container) {
            return $container
                ->getLocator()
                ->tour()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addOmsClient(Container $container): Container
    {
        $container[self::CLIENT_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addAuthClient(Container $container): Container
    {
        $container[self::CLIENT_AUTH] = function (Container $container) {
            return $container
                ->getLocator()
                ->auth()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addDepositMerchantConnectorClient(Container $container): Container
    {
        $container[self::CLIENT_DEPOSIT_MERCHANT_CONNECTOR] = function (Container $container) {
            return $container
                ->getLocator()
                ->depositMerchantConnector()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addDriverAppClient(Container $container): Container
    {
        $container[self::CLIENT_DRIVER_APP] = function (Container $container) {
            return $container
                ->getLocator()
                ->driverApp()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addDiscountClient(Container $container): Container
    {
        $container[self::CLIENT_DISCOUNT] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCancelOrderClient(Container $container): Container
    {
        $container[static::CLIENT_CANCEL_ORDER] = function (Container $container) {
            return $container
                ->getLocator()
                ->cancelOrder()
                ->client();
        };

        return $container;
    }
}
