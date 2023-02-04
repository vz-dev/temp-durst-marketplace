<?php


namespace Pyz\Zed\DeliveryArea;

use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaDeletePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaSavePluginInterface;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridge;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\ConcreteTimeSlotPostDeleteTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\ConcreteTimeSlotsPostSaveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\PostDeliveryAreaDeleteTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\PostDeliveryAreaSaveTouchPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Symfony\Component\Filesystem\Filesystem;

class DeliveryAreaDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_ABSENCE = 'FACADE_ABSENCE';
    public const FACADE_TOUR = 'FACADE_TOUR';
    public const FACADE_TAX = 'FACADE_TAX';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';
    public const FACADE_SOFTWARE_PACKAGE = 'FACADE_SOFTWARE_PACKAGE';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';

    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';

    public const CLIENT_QUEUE = 'CLIENT_QUEUE';

    public const FILESYSTEM = 'FILESYSTEM';

    public const POST_DELIVERY_AREA_SAVE_PLUGINS = 'POST_DELIVERY_AREA_SAVE_PLUGINS';
    public const POST_DELIVERY_AREA_DELETE_PLUGINS = 'POST_DELIVERY_AREA_DELETE_PLUGINS';

    public const POST_CONCRETE_TIME_SLOT_SAVE_PLUGINS = 'POST_CONCRETE_TIME_SLOT_SAVE_PLUGINS';
    public const POST_CONCRETE_TIME_SLOT_DELETE_PLUGINS = 'POST_CONCRETE_TIME_SLOT_DELETE_PLUGINS';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container) : Container
    {
        $container = $this->addAbsenceFacade($container);
        $container = $this->addTourFacade($container);
        $container = $this->addSalesQueryContainer($container);
        $container = $this->addTaxFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addDepositFacade($container);
        $container = $this->addDiscountFacade($container);

        $container = $this->addPostDeliveryAreaSavePlugins($container);
        $container = $this->addPostDeliveryAreaDeletePlugins($container);
        $container = $this->addPostConcreteTimeSlotSavePlugins($container);
        $container = $this->addPostConcreteTimeSlotDeletePlugins($container);
        $container = $this->addQueueClient($container);
        $container = $this->addMailFacade($container);
        $container = $this->addFilesystem($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addSoftwarePackageFacade($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addGraphMastersFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addAbsenceFacade(Container $container) : Container
    {
        $container[static::FACADE_ABSENCE] = function (Container $container) {
            return $container->getLocator()->absence()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTourFacade(Container $container) : Container
    {
        $container[static::FACADE_TOUR] = function (Container $container) {
            return $container->getLocator()->tour()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSalesQueryContainer(Container $container) : Container
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
    protected function addTaxFacade(Container $container) : Container
    {
        $container[static::FACADE_TAX] = function (Container $container) {
            return $container->getLocator()->tax()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTouchFacade(Container $container) : Container
    {
        $container[self::FACADE_TOUCH] = function (Container $container) {
            return new DeliveryAreaToTouchBridge($container->getLocator()->touch()->facade());
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPostDeliveryAreaSavePlugins(Container $container) : Container
    {
        $container[self::POST_DELIVERY_AREA_SAVE_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container
) {
            return $this->getPostDeliveryAreaSavePlugins();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPostDeliveryAreaDeletePlugins(Container $container) : Container
    {
        $container[self::POST_DELIVERY_AREA_DELETE_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container
) {
            return $this->getPostDeliveryAreaDeletePlugins();
        };

        return $container;
    }

    /**
     * @return PostDeliveryAreaSavePluginInterface[]
     */
    protected function getPostDeliveryAreaSavePlugins() : array
    {
        return [
            new PostDeliveryAreaSaveTouchPlugin(),
        ];
    }

    /**
     * @return PostDeliveryAreaDeletePluginInterface[]
     */
    protected function getPostDeliveryAreaDeletePlugins() : array
    {
        return [
            new PostDeliveryAreaDeleteTouchPlugin(),
        ];
    }

    /**
     * @return PostConcreteTimeSlotSavePluginInterface[]
     */
    protected function getConcreteTimeSlotPostSavePlugins() : array
    {
        return [
            new ConcreteTimeSlotsPostSaveTouchPlugin(),
        ];
    }

    /**
     * @return PostConcreteTimeSlotDeletePluginInterface[]
     */
    protected function getConcreteTimeSlotPostDeletePlugins() : array
    {
        return [
            new ConcreteTimeSlotPostDeleteTouchPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPostConcreteTimeSlotSavePlugins(Container $container) : Container
    {
        $container[self::POST_CONCRETE_TIME_SLOT_SAVE_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container
) {
            return $this->getConcreteTimeSlotPostSavePlugins();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPostConcreteTimeSlotDeletePlugins(Container $container) : Container
    {
        $container[self::POST_CONCRETE_TIME_SLOT_DELETE_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container
) {
            return $this->getConcreteTimeSlotPostDeletePlugins();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addDepositFacade(Container $container) : Container
    {
        $container[self::FACADE_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addQueueClient(Container $container): Container
    {
        $container[static::CLIENT_QUEUE] = function (Container $container) {
            return $container->getLocator()->queue()->client();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addMailFacade(Container $container): Container
    {
        $container[static::FACADE_MAIL] = function (Container $container) {
            return $container->getLocator()->mail()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addFilesystem(Container $container): Container
    {
        $container[static::FILESYSTEM] = function () {
            return new Filesystem();
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
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDiscountFacade(Container $container) : Container
    {
        $container[self::FACADE_DISCOUNT] = function (Container $container) {
            return $container->getLocator()->discount()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSoftwarePackageFacade(Container $container): Container
    {
        $container[self::FACADE_SOFTWARE_PACKAGE] = function (Container $container) {
            return $container->getLocator()->softwarePackage()->facade();
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
    protected function addGraphMastersFacade(Container $container): Container
    {
        $container[self::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container->getLocator()->graphMasters()->facade();
        };

        return $container;
    }
}
