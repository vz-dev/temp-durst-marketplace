<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector;

use Pyz\Shared\Absence\AbsenceConstants;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\Category\Communication\Plugin\CategoryDataPageMapPlugin;
use Pyz\Zed\Category\Communication\Plugin\CategoryNodeDataPageMapPlugin;
use Pyz\Zed\Collector\Communication\Plugin\AbsenceCollectorStoragePlugin;
use Pyz\Zed\Collector\Communication\Plugin\GMSettingsCollectorStoragePlugin;
use Pyz\Zed\Collector\Communication\Plugin\GMTimeSlotCollectorSearchPlugin;
use Pyz\Zed\Collector\Communication\Plugin\TimeslotCollectorSearchPlugin;
use Pyz\Zed\Collector\Communication\Plugin\TranslationCollectorStoragePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\DeliveryAreaDataPageMapPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\TimeslotDataPageMapPlugin;
use Pyz\Zed\GraphMasters\Communication\Plugin\GMTimeslotDataPageMapPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\BranchDataPageMapPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\PaymentProviderDataPageMapPlugin;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PriceDataPageMapPlugin;
use Pyz\Zed\ProductSearch\Communication\Plugin\ProductDataPageMapPlugin;
use Spryker\Zed\Collector\CollectorDependencyProvider as SprykerCollectorDependencyProvider;
use Spryker\Zed\Glossary\Business\Translation\TranslationManager;
use Spryker\Zed\Kernel\Container;

class CollectorDependencyProvider extends SprykerCollectorDependencyProvider
{
    public const SERVICE_DATA = 'SERVICE_DATA';

    public const FACADE_PROPEL = 'FACADE_PROPEL';
    public const FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';
    public const FACADE_SEARCH = 'FACADE_SEARCH';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_PRODUCT_OPTION = 'FACADE_PRODUCT_OPTION';
    public const FACADE_PRODUCT_IMAGE = 'FACADE_PRODUCT_IMAGE';
    public const FACADE_DELIVERY_AREA = 'FACADE_DELIVERY_AREA';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';

    public const QUERY_CONTAINER_CATEGORY = 'QUERY_CONTAINER_CATEGORY';
    public const QUERY_CONTAINER_DELIVERY_AREA = 'QUERY_CONTAINER_DELIVERY_AREA';
    public const QUERY_CONTAINER_PRODUCT_CATEGORY = 'QUERY_CONTAINER_PRODUCT_CATEGORY';
    public const QUERY_CONTAINER_PRODUCT_IMAGE = 'QUERY_CONTAINER_PRODUCT_IMAGE';
    public const QUERY_CONTAINER_PRODUCT_OPTION = 'QUERY_CONTAINER_PRODUCT_OPTION';
    public const QUERY_CONTAINER_GRAPHMASTERS = 'QUERY_CONTAINER_GRAPHMASTERS';
    public const QUERY_CONTAINER_ABSENCE = 'QUERY_CONTAINER_ABSENCE';

    public const PLUGIN_PRODUCT_DATA_PAGE_MAP = 'PLUGIN_PRODUCT_DATA_PAGE_MAP';
    public const PLUGIN_CATEGORY_NODE_DATA_PAGE_MAP = 'PLUGIN_CATEGORY_NODE_DATA_PAGE_MAP';
    public const PLUGIN_BRANCH_DATA_PAGE_MAP = 'PLUGIN_BRANCH_DATA_PAGE_MAP';
    public const PLUGIN_DELIVERY_AREA_DATA_PAGE_MAP = 'PLUGIN_DELIVERY_AREA_DATA_PAGE_MAP';
    public const PLUGIN_PRICE_DATA_PAGE_MAP = 'PLUGIN_PRICE_DATA_PAGE_MAP';
    public const PLUGIN_CATEGORY_DATA_PAGE_MAP = 'PLUGIN_CATEGORY_DATA_PAGE_MAP';
    public const PLUGIN_TIMESLOT_DATA_PAGE_MAP = 'PLUGIN_TIMESLOT_DATA_PAGE_MAP';
    public const PLUGIN_PAYMENT_PROVIDER_DATA_PAGE_MAP = 'PLUGIN_PAYMENT_PROVIDER_DATA_PAGE_MAP';
    public const PLUGIN_GM_TIMESLOT_DATA_PAGE_MAP = 'PLUGIN_GM_TIMESLOT_DATA_PAGE_MAP';

    public const TIME_SLOT_SEARCH_PLUGINS = 'TIME_SLOT_SEARCH_PLUGINS';
    public const GM_TIME_SLOT_SEARCH_PLUGINS = 'GM_TIME_SLOT_SEARCH_PLUGINS';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addPropelFacade($container);
        $container = $this->addCategoryQueryContainer($container);
        $container = $this->addProductCategoryQueryContainer($container);
        $container = $this->addSearchFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addProductImageFacade($container);
        $container = $this->addProductImageQueryContainer($container);
        $container = $this->addPriceProductFacade($container);
        $container = $this->addUtilDataReaderService($container);
        $container = $this->addProductOptionFacade($container);
        $container = $this->addProductOptionQueryContainer($container);
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addDeliveryAreaQueryContainer($container);
        $container = $this->addDepositFacade($container);
        $container = $this->addGraphmastersQueryContainer($container);
        $container = $this->addAbsenceQueryContainer($container);

        $container = $this->addSearchPlugins($container);
        $container = $this->addTimeSlotSearchPlugins($container);
        $container = $this->addGMTimeSlotSearchPlugins($container);
        $container = $this->addStoragePlugins($container);

        $container = $this->addPageMapPlugins($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPageMapPlugins(Container $container): Container
    {
        $container = $this->addProductPageMapPlugin($container);
        $container = $this->addCategoryNodePageMapPlugin($container);
        $container = $this->addDeliveryAreaPageMapPlugin($container);
        $container = $this->addBranchPageMapPlugin($container);
        $container = $this->addPricePageMapPlugin($container);
        $container = $this->addCategoryPageMapPlugin($container);
        $container = $this->addTimeSlotPageMapPlugin($container);
        $container = $this->addPaymentProviderPageMapPlugin($container);
        $container = $this->addGMTimeSlotPageMapPlugin($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPaymentProviderPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_PAYMENT_PROVIDER_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new PaymentProviderDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTimeSlotPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_TIMESLOT_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new TimeslotDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addCategoryPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_CATEGORY_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new CategoryDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPricePageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_PRICE_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new PriceDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addBranchPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_BRANCH_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new BranchDataPageMapPlugin();
        };

         return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addDeliveryAreaPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_DELIVERY_AREA_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new DeliveryAreaDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_PRODUCT_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new ProductDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addCategoryNodePageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_CATEGORY_NODE_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new CategoryNodeDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addStoragePlugins(Container $container): Container
    {
        $container[self::STORAGE_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return [
                TranslationManager::TOUCH_TRANSLATION => new TranslationCollectorStoragePlugin(),
                GraphMastersConstants::GRAPHMASTERS_SETTINGS_RESOURCE_TYPE => new GMSettingsCollectorStoragePlugin(),
                AbsenceConstants::ABSENCE_RESOURCE_TYPE => new AbsenceCollectorStoragePlugin(),
            ];
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTimeSlotSearchPlugins(Container $container): Container
    {
        $container[self::TIME_SLOT_SEARCH_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return [
                DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT => new TimeslotCollectorSearchPlugin(),
            ];
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSearchPlugins(Container $container): Container
    {
        $container[self::SEARCH_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return [
            ];
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductOptionQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT_OPTION] = function (Container $container) {
            return $container->getLocator()->productOption()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductOptionFacade(Container $container): Container
    {
        $container[static::FACADE_PRODUCT_OPTION] = function (Container $container) {
            return $container->getLocator()->productOption()->facade();
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
    protected function addPropelFacade(Container $container): Container
    {
        $container[self::FACADE_PROPEL] = function (Container $container) {
            return $container->getLocator()->propel()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addCategoryQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_CATEGORY] = function (Container $container) {
            return $container->getLocator()->category()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductCategoryQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_PRODUCT_CATEGORY] = function (Container $container) {
            return $container->getLocator()->productCategory()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSearchFacade(Container $container): Container
    {
        $container[self::FACADE_SEARCH] = function (Container $container) {
            return $container->getLocator()->search()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductFacade(Container $container): Container
    {
        $container[self::FACADE_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductImageFacade(Container $container): Container
    {
        $container[static::FACADE_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductImageQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addPriceProductFacade(Container $container): Container
    {
        $container[self::FACADE_PRICE_PRODUCT] = function (Container $container) {
            return $container->getLocator()->priceProduct()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addUtilDataReaderService(Container $container): Container
    {
        $container[static::SERVICE_DATA] = function (Container $container) {
            return $container->getLocator()->utilDataReader()->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDeliveryAreaQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->queryContainer();
        };

        return $container;
    }

    /**
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
     * @param Container $container
     *
     * @return Container
     */
    protected function addGMTimeSlotSearchPlugins(Container $container): Container
    {
        $container[self::GM_TIME_SLOT_SEARCH_PLUGINS] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return [
                GraphMastersConstants::GRAPHMASTERS_TIMESLOT_RESOURCE_TYPE => new GMTimeslotCollectorSearchPlugin(),
            ];
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addGMTimeSlotPageMapPlugin(Container $container): Container
    {
        $container[self::PLUGIN_GM_TIMESLOT_DATA_PAGE_MAP] = function (
            /** @noinspection PhpUnusedParameterInspection */
            Container $container) {
            return new GMTimeslotDataPageMapPlugin();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphmastersQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_GRAPHMASTERS] = function (
            Container $container) {
            return $container->getLocator()->graphMasters()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addAbsenceQueryContainer(Container $container): Container
    {
        $container[self::QUERY_CONTAINER_ABSENCE] = function (
            Container $container) {
            return $container->getLocator()->absence()->queryContainer();
        };

        return $container;
    }
}
