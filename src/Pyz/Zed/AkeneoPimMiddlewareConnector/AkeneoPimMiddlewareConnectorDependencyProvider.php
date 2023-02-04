<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - AkeneoPimMiddlewareConnectorDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.03.18
 * Time: 14:39
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector;

use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\AttributeDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\CategoryDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\ProductAbstractDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\ProductAbstractStoresDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\ProductConcreteDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\ProductPriceDataImporterPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\EnrichAttributesTranslatorFunctionPlugin;
use Spryker\Zed\DataImport\Dependency\Facade\DataImportToTouchBridge;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\AkeneoPimMiddlewareConnectorDependencyProvider as SprykerEcoAkeneoPimMiddlewareConnectorDependencyProvider;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddAbstractSkuIfNotExistTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddAttributeOptionsTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddAttributeValuesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddFamilyAttributeTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddMissingAttributesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddMissingLocalesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AddUrlToLocalizedAttributesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\AttributeEmptyTranslationToKeyTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\LabelsToLocaleIdsTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\LabelsToLocalizedAttributeNamesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\LocaleKeysToIdsTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\MarkAsSuperAttributeTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\MeasureUnitToIntTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\MoveLocalizedAttributesToAttributesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\PriceSelectorTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\SkipItemsWithoutParentTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\ValuesToAttributesTranslatorFunctionPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\ValuesToLocalizedAttributesTranslatorFunctionPlugin;


class AkeneoPimMiddlewareConnectorDependencyProvider extends SprykerEcoAkeneoPimMiddlewareConnectorDependencyProvider
{
    const FACADE_TOUCH = 'FACADE_TOUCH';
    const FACADE_AVAILABILITY = 'FACADE_AVAILABILITY';

    public function provideBusinessLayerDependencies(Container $container)
    {
        $container =  parent::provideBusinessLayerDependencies($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addAvailabilityFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_CATEGORY_IMPORTER_PLUGIN] = function () {
            return new CategoryDataImporterPlugin();
        };
        return $container;
    }
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAttributeDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_ATTRIBUTE_IMPORTER_PLUGIN] = function () {
            return new AttributeDataImporterPlugin();
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductAbstractDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_PRODUCT_ABSTRACT_IMPORTER_PLUGIN] = function () {
            return new ProductAbstractDataImporterPlugin();
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductConcreteDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_PRODUCT_CONCRETE_IMPORTER_PLUGIN] = function () {
            return new ProductConcreteDataImporterPlugin();
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductPriceDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_PRODUCT_PRICE_IMPORTER_PLUGIN] = function () {
            return new ProductPriceDataImporterPlugin();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductAbstractStoresDataImporterPlugin(Container $container): Container
    {
        $container[static::AKENEO_PIM_MIDDLEWARE_PRODUCT_ABSTRACT_STORES_IMPORTER_PLUGIN] = function () {
            return new ProductAbstractStoresDataImporterPlugin();
        };

        return $container;
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\TranslatorFunction\TranslatorFunctionPluginInterface[]
     */
    protected function getAkeneoPimTranslatorFunctionPlugins(): array
    {
        return [
            new AddAbstractSkuIfNotExistTranslatorFunctionPlugin(),
            new AddAttributeOptionsTranslatorFunctionPlugin(),
            new AddAttributeValuesTranslatorFunctionPlugin(),
            new AddFamilyAttributeTranslatorFunctionPlugin(),
            new AddMissingAttributesTranslatorFunctionPlugin(),
            new AddMissingLocalesTranslatorFunctionPlugin(),
            new AddUrlToLocalizedAttributesTranslatorFunctionPlugin(),
            new AttributeEmptyTranslationToKeyTranslatorFunctionPlugin(),
            new EnrichAttributesTranslatorFunctionPlugin(),
            new LabelsToLocalizedAttributeNamesTranslatorFunctionPlugin(),
            new LocaleKeysToIdsTranslatorFunctionPlugin(),
            new MarkAsSuperAttributeTranslatorFunctionPlugin(),
            new MeasureUnitToIntTranslatorFunctionPlugin(),
            new MoveLocalizedAttributesToAttributesTranslatorFunctionPlugin(),
            new PriceSelectorTranslatorFunctionPlugin(),
            new ValuesToAttributesTranslatorFunctionPlugin(),
            new ValuesToLocalizedAttributesTranslatorFunctionPlugin(),
            new MeasureUnitToIntTranslatorFunctionPlugin(),
            new LabelsToLocaleIdsTranslatorFunctionPlugin(),
            new SkipItemsWithoutParentTranslatorFunctionPlugin(),
        ];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container): Container
    {
        $container[static::FACADE_TOUCH] = function (Container $container) {
            return new DataImportToTouchBridge(
                $container->getLocator()->touch()->facade()
            );
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAvailabilityFacade(Container $container): Container
    {
        $container[static::FACADE_AVAILABILITY] = function (Container $container) {
            return $container->getLocator()->availability()->facade();
        };
        return $container;
    }

}