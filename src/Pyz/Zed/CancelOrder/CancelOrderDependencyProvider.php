<?php
/**
 * Durst - project - CancelOrderDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 09:01
 */

namespace Pyz\Zed\CancelOrder;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * Class CancelOrderDependencyProvider
 * @package Pyz\Zed\CancelOrder
 */
class CancelOrderDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_JWT = 'FACADE_JWT';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_TOUR = 'FACADE_TOUR';
    public const FACADE_CANCEL_ORDER = 'FACADE_CANCEL_ORDER';
    public const FACADE_OMS = 'FACADE_OMS';
    public const FACADE_DRIVER = 'FACADE_DRIVER';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_HEIDELPAY_REST = 'FACADE_HEIDELPAY_REST';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_AUTH = 'FACADE_AUTH';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';
    public const FACADE_STATE_MACHINE = 'FACADE_STATE_MACHINE';
    public const FACADE_EDIFACT ='FACADE_EDIFACT';

    public const QUERY_CONTYINER_CANCEL_ORDER = 'QUERY_CONTYINER_CANCEL_ORDER';
    public const QUERY_CONTAINER_OMS = 'QUERY_CONTAINER_OMS';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addJwtFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addTourFacade($container);
        $container = $this->addCancelOrderFacade($container);
        $container = $this->addDriverFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addCancelOrderFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addTourFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addHeidelpayRestFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addAuthFacade($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addStateMachineFacade($container);
        $container = $this->addEdifactFacade($container);

        $container = $this->addCancelOrderQueryContainer($container);
        $container = $this->addOmsQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addJwtFacade(Container $container): Container
    {
        $container[static::FACADE_JWT] = function (Container $container) {
            return $container
                ->getLocator()
                ->jwt()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCancelOrderFacade(Container $container): Container
    {
        $container[static::FACADE_CANCEL_ORDER] = function (Container $container) {
            return $container
                ->getLocator()
                ->cancelOrder()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDriverFacade(Container $container): Container
    {
        $container[static::FACADE_DRIVER] = function (Container $container) {
            return $container
                ->getLocator()
                ->driver()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addHeidelpayRestFacade(Container $container): Container
    {
        $container[static::FACADE_HEIDELPAY_REST] = function (Container $container) {
            return $container
                ->getLocator()
                ->heidelpayRest()
                ->facade();
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
            return $container
                ->getLocator()
                ->mail()
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
    protected function addAuthFacade(Container $container): Container
    {
        $container[static::FACADE_AUTH] = function (Container $container) {
            return $container
                ->getLocator()
                ->auth()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStateMachineFacade(Container $container): Container
    {
        $container[static::FACADE_STATE_MACHINE] = function (Container $container) {
            return $container
                ->getLocator()
                ->stateMachine()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEdifactFacade(Container $container): Container
    {
        $container[static::FACADE_EDIFACT] = function (Container $container) {
            return $container
                ->getLocator()
                ->edifact()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCancelOrderQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTYINER_CANCEL_ORDER] = function (Container $container) {
            return $container
                ->getLocator()
                ->cancelOrder()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
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
}
