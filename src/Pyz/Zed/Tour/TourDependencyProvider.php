<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 12:48
 */

namespace Pyz\Zed\Tour;

use Pyz\Zed\Tour\Dependency\Facade\TourToMailBridge;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class TourDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_DELIVERY_AREA = 'FACADE_DELIVERY_AREA';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';
    public const FACADE_EDIFACT = 'FACADE_EDIFACT';
    public const FACADE_OMS = 'FACADE_OMS';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_SOFTWARE_PACKAGE = 'FACADE_SOFTWARE_PACKAGE';
    public const FACADE_REFUND = 'FACADE_REFUND';
    public const FACADE_AUTH = 'FACADE_AUTH';
    public const FACADE_DRIVER = 'FACADE_DRIVER';
    public const FACADE_STATE_MACHINE = 'FACADE_STATE_MACHINE';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_GRAPHHOPPER = 'FACADE_GRAPHHOPPER';
    public const FACADE_BILLING = 'FACADE_BILLING';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';

    public const QUERY_CONTAINER_DISCOUNT = 'QUERY_CONTAINER_DISCOUNT';
    public const QUERY_CONTAINER_OMS = 'QUERY_CONTAINER_OMS';
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';
    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';
    public const QUERY_CONTAINER_BILLING = 'QUERY_CONTAINER_BILLING';
    public const QUERY_CONTAINER_GRAPHMASTERS = 'QUERY_CONTAINER_GRAPHMASTERS';

    public const PLUGIN_CONCRETE_TOUR_HYDRATOR = 'PLUGIN_CONCRETE_TOUR_HYDRATOR';

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addSequenceNumberFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addOmsQueryContainer($container);
        $container = $this->addSalesQueryContainer($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addSoftwarePackageFacade($container);
        $container = $this->addRefundFacade($container);
        $container = $this->addEdifactFacade($container);
        $container = $this->addDriverFacade($container);
        $container = $this->addStateMachineFacade($container);
        $container = $this->addBillingFacade($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addDiscountQueryContainer($container);
        $container = $this->addGraphMastersFacade($container);
        $container = $this->addGraphMastersQueryContainer($container);

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addOmsFacade($container);
        $container = $this->addOmsQueryContainer($container);
        $container = $this->addProductQueryContainer($container);
        $container = $this->addEdifactFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addAuthFacade($container);
        $container = $this->addMailFacade($container);
        $container = $this->addGraphhopperFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addBillingFacade($container);
        $container = $this->addBillingQueryContainer($container);
        $container = $this->addGraphMastersFacade($container);
        $container = $this->addGraphMastersQueryContainer($container);

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);

        $container = $this->addDepositFacade($container);
        $container = $this->addProductQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addDeliveryAreaFacade(Container $container): Container
    {
        $container[static::FACADE_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSequenceNumberFacade(Container $container): Container
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container->getLocator()->sequenceNumber()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return $container->getLocator()->oms()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addOmsQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_OMS] = function (Container $container) {
            return $container->getLocator()->oms()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSalesQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->queryContainer();
        };

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    protected function addDepositFacade(Container $container): Container
    {
        $container[self::FACADE_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->facade();
        };

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    protected function addEdifactFacade(Container $container): Container
    {
        $container[self::FACADE_EDIFACT] = function (Container $container) {
            return $container->getLocator()->edifact()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTouchFacade(Container $container): Container
    {
        $container[self::FACADE_TOUCH] = function (Container $container) {
            return $container->getLocator()->touch()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSoftwarePackageFacade(Container $container): Container
    {
        $container[static::FACADE_SOFTWARE_PACKAGE] = function (Container $container) {
            return $container->getLocator()->softwarePackage()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addRefundFacade(Container $container): Container
    {
        $container[self::FACADE_REFUND] = function (Container $container) {
            return $container->getLocator()->refund()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
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
     * @param Container $container
     * @return Container
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
     * @param Container $container
     * @return Container
     */
    protected function addStateMachineFacade(Container $container): Container
    {
        $container[static::FACADE_STATE_MACHINE] = function (Container $container) {
            return new TourToStateMachineBridge(
                $container
                    ->getLocator()
                    ->stateMachine()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addMailFacade(Container $container): Container
    {
        $container[self::FACADE_MAIL] = function (Container $container) {
            return new TourToMailBridge($container->getLocator()->mail()->facade());
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addGraphhopperFacade(Container $container): Container
    {
        $container[self::FACADE_GRAPHHOPPER] = function (Container $container) {
            return $container->getLocator()->graphhopper()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addBillingFacade(Container $container): Container
    {
        $container[self::FACADE_BILLING] = function (Container $container) {
            return $container->getLocator()->billing()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addIntegraFacade(Container $container): Container
    {
        $container[self::FACADE_INTEGRA] = function (Container $container) {
            return $container->getLocator()->integra()->facade();
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
            return $container->getLocator()->discount()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addBillingQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_BILLING] = function (Container $container) {
            return $container
                ->getLocator()
                ->billing()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphMastersFacade(Container $container): Container
    {
        $container[self::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container->getLocator()->graphMasters()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphMastersQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_GRAPHMASTERS] = function (Container $container) {
            return $container->getLocator()->graphMasters()->queryContainer();
        };

        return $container;
    }
}
