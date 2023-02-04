<?php

namespace Pyz\Zed\Integra;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Symfony\Component\Filesystem\Filesystem;

class IntegraDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_CURRENCY = 'FACADE_CURRENCY';
    public const FACADE_TAX = 'FACADE_TAX';
    public const FACADE_OMS = 'FACADE_OMS';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';
    public const FACADE_PDF = 'FACADE_PDF';

    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';
    public const QUERY_CONTAINER_MERCHANT = 'QUERY_CONTAINER_MERCHANT';
    public const QUERY_CONTAINER_REFUND = 'QUERY_CONTAINER_REFUND';
    public const QUERY_CONTAINER_DELIVERY_AREA = 'QUERY_CONTAINER_DELIVERY_AREA';
    public const QUERY_CONTAINER_TOUR = 'QUERY_CONTAINER_TOUR';
    public const QUERY_CONTAINER_MERCHANT_PRICE = 'QUERY_CONTAINER_MERCHANT_PRICE';
    public const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';

    public const SERVICE_SOAP_REQUEST = 'SERVICE_SOAP_REQUEST';

    public const FILE_SYSTEM = 'FILE_SYSTEM';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $this->addSoapRequestService($container);
        $this->addSalesFacade($container);
        $this->addMoneyFacade($container);
        $this->addCurrencyFacade($container);
        $this->addTaxFacade($container);
        $this->addOmsFacade($container);
        $this->addDepositFacade($container);
        $this->addPdfFacade($container);
        $this->addFileSystem($container);
        $this->addMerchantQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $this->addMerchantQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $this->addSalesQueryContainer($container);
        $this->addRefundQueryContainer($container);
        $this->addDeliveryAreaQueryContainer($container);
        $this->addTourQueryContainer($container);
        $this->addMerchantPriceQueryContainer($container);
        $this->addDepositQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     */
    protected function addSalesQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_SALES] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addMerchantQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_MERCHANT] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addRefundQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_REFUND] = function (Container $container) {
            return $container
                ->getLocator()
                ->refund()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addSoapRequestService(Container $container): void
    {
        $container[static::SERVICE_SOAP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->soapRequest()
                ->service();
        };
    }

    /**
     * @param Container $container
     */
    protected function addSalesFacade(Container $container): void
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->facade();
        };
    }

    /**
     * @param Container $container
     */
    protected function addDeliveryAreaQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_DELIVERY_AREA] = function (Container $container) {
            return $container
                ->getLocator()
                ->deliveryArea()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addMoneyFacade(Container $container): void
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return $container
                ->getLocator()
                ->money()
                ->facade();
        };
    }

    /**
     * @param Container $container
     */
    protected function addCurrencyFacade(Container $container): void
    {
        $container[static::FACADE_CURRENCY] = function (Container $container) {
            return $container
                ->getLocator()
                ->currency()
                ->facade();
        };
    }

    /**
     * @param Container $container
     */
    protected function addTourQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_TOUR] = function (Container $container) {
            return $container
                ->getLocator()
                ->tour()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addMerchantPriceQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_MERCHANT_PRICE] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchantPrice()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addDepositQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return $container
                ->getLocator()
                ->deposit()
                ->queryContainer();
        };
    }

    /**
     * @param Container $container
     */
    protected function addTaxFacade(Container $container): void
    {
        $container[static::FACADE_TAX] = function (Container $container) {
            return $container
                ->getLocator()
                ->tax()
                ->facade();
        };
    }

    /**
     * @param Container $container
     */
    protected function addOmsFacade(Container $container): void
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->facade();
        };
    }

    /**
     * @param Container $container
     */
    protected function addDepositFacade(Container $container): void
    {
        $container[static::FACADE_DEPOSIT] = function (Container $container) {
            return $container
                ->getLocator()
                ->deposit()
                ->facade();
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addFileSystem(Container $container): void
    {
        $container[static::FILE_SYSTEM] = function () {
            return new Filesystem();
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return void
     */
    protected function addPdfFacade(Container $container): void
    {
        $container[static::FACADE_PDF] = function (Container $container) {
            return $container
                ->getLocator()
                ->pdf()
                ->facade();
        };
    }
}
