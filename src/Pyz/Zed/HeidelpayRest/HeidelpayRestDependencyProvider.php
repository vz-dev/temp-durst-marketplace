<?php
/**
 * Durst - project - HeidelpayRestDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:09
 */

namespace Pyz\Zed\HeidelpayRest;

use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridge;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridge;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class HeidelpayRestDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_OMS = 'FACADE_OMS';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_BILLING = 'FACADE_BILLING';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';

    /**
     * {@inheritdoc}
     *
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addMoneyFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addBillingFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addDiscountFacade($container);

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addSalesFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addGraphMastersFacade($container);
        $container = $this->addDiscountFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container): Container
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new HeidelpayRestToMoneyBridge($container->getLocator()->money()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return new HeidelpayRestToSalesBridge($container->getLocator()->sales()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return new HeidelpayRestToOmsBridge($container->getLocator()->oms()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMailFacade(Container $container): Container
    {
        $container[static::FACADE_MAIL] = function (Container $container) {
            return $container->getLocator()->mail()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container): Container
    {
        $container[static::FACADE_TOUCH] = function (Container $container) {
            return $container->getLocator()->touch()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDiscountFacade(Container $container): Container
    {
        $container[static::FACADE_DISCOUNT] = function (Container $container) {
            return $container->getLocator()->discount()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBillingFacade(Container $container): Container
    {
        $container[static::FACADE_BILLING] = function (Container $container) {
            return $container
                ->getLocator()
                ->billing()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addGraphMastersFacade(Container $container): Container
    {
        $container[static::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container
                ->getLocator()
                ->graphMasters()
                ->facade();
        };

        return $container;
    }
}
