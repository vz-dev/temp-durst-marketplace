<?php

namespace Pyz\Zed\GraphMasters;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class GraphMastersDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_HTTP_REQUEST = 'FACADE_HTTP_REQUEST';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_TOUR = 'FACADE_TOUR';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';

    public const QUERY_CONTAINER_DELIVERY_AREA = 'QUERY_CONTAINER_DELIVERY_AREA';
    public const QUERY_CONTAINER_MERCHANT = 'QUERY_CONTAINER_MERCHANT';
    public const QUERY_CONTAINER_OMS = 'QUERY_CONTAINER_OMS';
    public const QUERY_CONTAINER_DISCOUNT = 'QUERY_CONTAINER_DISCOUNT';

    public const SERVICE_HTTP_REQUEST = 'SERVICE_HTTP_REQUEST';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantQueryContainer($container);
        $container = $this->addDeliveryAreaQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = $this->addHttpRequestService($container);
        $container = $this->addHttpRequestFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addSequenceNumberFacade($container);
        $container = $this->addTourFacade($container);
        $container = $this->addOmsQueryContainer($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addDiscountQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addHttpRequestService(Container $container): Container
    {
        $container[static::SERVICE_HTTP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->httpRequest()
                ->service();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addHttpRequestFacade(Container $container): Container
    {
        $container[static::FACADE_HTTP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->httpRequest()
                ->facade();
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

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_MERCHANT] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDeliveryAreaQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_DELIVERY_AREA] = function (Container $container) {
            return $container
                ->getLocator()
                ->deliveryArea()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTouchFacade(Container $container): Container
    {
        $container[static::FACADE_TOUCH] = function (Container $container) {
            return $container
                ->getLocator()
                ->touch()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSequenceNumberFacade(Container $container): Container
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container
                ->getLocator()
                ->sequenceNumber()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTourFacade(Container $container): Container
    {
        $container[static::FACADE_TOUR] = function (Container $container) {
            return $container
                ->getLocator()
                ->tour()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addOmsQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addIntegraFacade(Container $container): Container
    {
        $container[static::FACADE_INTEGRA] = function (Container $container) {
            return $container
                ->getLocator()
                ->integra()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDiscountQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_DISCOUNT] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->queryContainer();
        };

        return $container;
    }
}
