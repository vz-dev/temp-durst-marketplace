<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Business;

use Everon\Component\CriteriaBuilder\CriteriaBuilderInterface;
use Exception;
use Pyz\Zed\Absence\Persistence\AbsenceQueryContainerInterface;
use Pyz\Zed\Category\Persistence\CategoryQueryContainerInterface;
use Pyz\Zed\Collector\Business\Search\BranchCollector;
use Pyz\Zed\Collector\Business\Search\CategoryCollector;
use Pyz\Zed\Collector\Business\Search\CategoryNodeCollector as SearchCategoryNodeCollector;
use Pyz\Zed\Collector\Business\Search\DeliveryAreaCollector;
use Pyz\Zed\Collector\Business\Search\GMTimeSlotCollector;
use Pyz\Zed\Collector\Business\Search\PaymentProviderCollector;
use Pyz\Zed\Collector\Business\Search\PriceCollector;
use Pyz\Zed\Collector\Business\Search\ProductCollector as SearchProductCollector;
use Pyz\Zed\Collector\Business\Search\TimeSlotCollector;
use Pyz\Zed\Collector\Business\Storage\AbsenceCollector;
use Pyz\Zed\Collector\Business\Storage\AttributeMapCollector;
use Pyz\Zed\Collector\Business\Storage\AvailabilityCollector;
use Pyz\Zed\Collector\Business\Storage\CategoryNodeCollector as StorageCategoryNodeCollector;
use Pyz\Zed\Collector\Business\Storage\ConcreteTimeSlotCollector;
use Pyz\Zed\Collector\Business\Storage\GMSettingsCollector;
use Pyz\Zed\Collector\Business\Storage\NavigationCollector;
use Pyz\Zed\Collector\Business\Storage\ProductAbstractCollector as StorageProductCollector;
use Pyz\Zed\Collector\Business\Storage\ProductConcreteCollector;
use Pyz\Zed\Collector\Business\Storage\ProductOptionCollector;
use Pyz\Zed\Collector\Business\Storage\RedirectCollector;
use Pyz\Zed\Collector\Business\Storage\TranslationCollector;
use Pyz\Zed\Collector\Business\Storage\UrlCollector;
use Pyz\Zed\Collector\CollectorConfig;
use Pyz\Zed\Collector\CollectorDependencyProvider;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\GMTimeSlotCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\TimeSlotCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\AttributeMapCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\AvailabilityCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\AvailabilityCollectorQuery as StorageAvailabilityCollectorPropelQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\ConcreteTimeSlotCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\ConcreteTimeSlotCollectorQuery as StorageConcreteTimeSlotCollectorPropelQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\RedirectCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\RedirectCollectorQuery as StorageRedirectCollectorPropelQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\TranslationCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Propel\TranslationCollectorQuery as StorageTranslationCollectorPropelQuery;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactory;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker;
use Spryker\Zed\Collector\Business\CollectorBusinessFactory as SprykerCollectorBusinessFactory;
use Spryker\Zed\Collector\Business\Exporter\CollectorExporter;
use Spryker\Zed\Collector\Business\Exporter\ExportMarker;
use Spryker\Zed\Collector\Business\Exporter\MarkerInterface;
use Spryker\Zed\Collector\Business\Exporter\Reader\Search\ElasticsearchMarkerReader;
use Spryker\Zed\Collector\Business\Exporter\Reader\Search\ElasticsearchReader;
use Spryker\Zed\Collector\Business\Exporter\SearchExporter;
use Spryker\Zed\Collector\Business\Exporter\Writer\Search\ElasticsearchMarkerWriter;
use Spryker\Zed\Collector\Business\Exporter\Writer\Search\ElasticsearchUpdateWriter;
use Spryker\Zed\Collector\Business\Exporter\Writer\Search\ElasticsearchWriter;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Storage\StorageInstanceBuilder;
use Spryker\Zed\Collector\Persistence\Collector\AbstractCollectorQuery;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface;
use Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface;
use Spryker\Zed\Propel\Business\PropelFacadeInterface;
use Spryker\Zed\Search\Business\SearchFacadeInterface;

/**
 * @method CollectorConfig getConfig()
 */
class CollectorBusinessFactory extends SprykerCollectorBusinessFactory
{
    /**
     * @return SearchProductCollector
     */
    public function createSearchProductCollector()
    {
        $searchProductCollector = new SearchProductCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_PRODUCT_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchProductCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchProductCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchProductCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('ProductCollectorQuery')
        );

        return $searchProductCollector;
    }

    /**
     * @return DeliveryAreaCollector
     */
    public function createSearchDeliveryAreaCollector(): DeliveryAreaCollector
    {
        $searchDeliveryAreaCollector = new DeliveryAreaCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_DELIVERY_AREA_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchDeliveryAreaCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchDeliveryAreaCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchDeliveryAreaCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('DeliveryAreaCollectorQuery')
        );

        return $searchDeliveryAreaCollector;
    }

    /**
     * @return BranchCollector
     */
    public function createSearchBranchCollector(): BranchCollector
    {
        $searchBranchCollector = new BranchCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_BRANCH_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchBranchCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchBranchCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchBranchCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('BranchCollectorQuery')
        );

        return $searchBranchCollector;
    }

    /**
     * @return PriceCollector
     */
    public function createSearchPriceCollector(): PriceCollector
    {
        $searchPriceCollector = new PriceCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_PRICE_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchPriceCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchPriceCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchPriceCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('PriceCollectorQuery')
        );

        return $searchPriceCollector;
    }

    /**
     * @return CategoryCollector
     */
    public function createSearchCategoryCollector(): CategoryCollector
    {
        $searchCategoryCollector = new CategoryCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_CATEGORY_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchCategoryCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchCategoryCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchCategoryCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('CategoryCollectorQuery')
        );

        return $searchCategoryCollector;
    }

    /**
     * @return \Pyz\Zed\Collector\Business\Search\TimeslotCollector
     */
    public function createSearchTimeSlotCollector(): TimeSlotCollector
    {
        $searchTimeSlotCollector = new TimeSlotCollector(
            $this->getUtilDataReaderService(),
            $this->getDeliveryAreaFacade(),
            $this->getDeliveryAreaQueryContainer(),
            $this->getDepositFacade(),
            $this->getConfig()
        );

        $searchTimeSlotCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchTimeSlotCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchTimeSlotCollector->setQueryBuilder(
            $this->createTimeSlotCollectorQuery()
        );

        return $searchTimeSlotCollector;
    }

    /**
     * @return TimeSlotCollectorQuery
     */
    protected function createTimeSlotCollectorQuery() : TimeSlotCollectorQuery
    {
        return new TimeSlotCollectorQuery(
            $this->getConfig()
        );
    }

    /**
     * @return PaymentProviderCollector
     */
    public function createSearchPaymentProviderCollector(): PaymentProviderCollector
    {
        $searchPaymentProviderCollector = new PaymentProviderCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_PAYMENT_PROVIDER_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $searchPaymentProviderCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchPaymentProviderCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchPaymentProviderCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('PaymentProviderCollectorQuery')
        );

        return $searchPaymentProviderCollector;
    }

    /**
     * @return StorageCategoryNodeCollector
     */
    public function createStorageCategoryNodeCollector(): StorageCategoryNodeCollector
    {
        $storageCategoryNodeCollector = new StorageCategoryNodeCollector(
            $this->getUtilDataReaderService()
        );

        $storageCategoryNodeCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageCategoryNodeCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $storageCategoryNodeCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('CategoryNodeCollectorQuery')
        );

        return $storageCategoryNodeCollector;
    }

    /**
     * @return SearchCategoryNodeCollector
     */
    public function createSearchCategoryNodeCollector(): SearchCategoryNodeCollector
    {
        $categoryNodeCollector = new SearchCategoryNodeCollector(
            $this->getUtilDataReaderService(),
            $this->getProvidedDependency(CollectorDependencyProvider::PLUGIN_CATEGORY_NODE_DATA_PAGE_MAP),
            $this->getSearchFacade()
        );

        $categoryNodeCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $categoryNodeCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $categoryNodeCollector->setQueryBuilder(
            $this->createSearchPdoQueryAdapterByName('CategoryNodeCollectorQuery')
        );

        return $categoryNodeCollector;
    }

    /**
     * @return NavigationCollector
     */
    public function createStorageNavigationCollector(): NavigationCollector
    {
        $storageNavigationCollector = new NavigationCollector(
            $this->getUtilDataReaderService()
        );

        $storageNavigationCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageNavigationCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $storageNavigationCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('NavigationCollectorQuery')
        );

        return $storageNavigationCollector;
    }

    /**
     * @return StorageProductCollector
     */
    public function createStorageProductAbstractCollector()
    {
        $storageProductCollector = new StorageProductCollector(
            $this->getUtilDataReaderService(),
            $this->getCategoryQueryContainer(),
            $this->getProductCategoryQueryContainer(),
            $this->getProductImageQueryContainer(),
            $this->getProductFacade(),
            $this->getPriceProductFacade(),
            $this->getProductImageFacade()
        );

        $storageProductCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageProductCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $storageProductCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('ProductCollectorQuery')
        );

        return $storageProductCollector;
    }

    /**
     * @return RedirectCollector
     */
    public function createStorageRedirectCollector(): RedirectCollector
    {
        $storageRedirectCollector = new RedirectCollector(
            $this->getUtilDataReaderService()
        );

        $storageRedirectCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageRedirectCollector->setQueryBuilder(
            $this->createStorageRedirectCollectorPropelQuery()
        );
        $storageRedirectCollector->setConfig($this->getConfig());

        return $storageRedirectCollector;
    }

    /**
     * @return TranslationCollector
     */
    public function createStorageTranslationCollector(): TranslationCollector
    {
        $storageTranslationCollector = new TranslationCollector(
            $this->getUtilDataReaderService()
        );

        $storageTranslationCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageTranslationCollector->setQueryBuilder(
            $this->createStorageTranslationCollectorPropelQuery()
        );
        $storageTranslationCollector->setConfig($this->getConfig());

        return $storageTranslationCollector;
    }

    /**
     * @return UrlCollector
     */
    public function createStorageUrlCollector(): UrlCollector
    {
        $storageUrlCollector = new UrlCollector(
            $this->getUtilDataReaderService()
        );

        $storageUrlCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageUrlCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $storageUrlCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('UrlCollectorQuery')
        );

        return $storageUrlCollector;
    }

    /**
     * @return ProductConcreteCollector
     */
    public function createStorageProductConcreteCollector(): ProductConcreteCollector
    {
        $productConcreteCollector = new ProductConcreteCollector(
            $this->getUtilDataReaderService(),
            $this->getProductFacade(),
            $this->getPriceProductFacade(),
            $this->getProductImageQueryContainer(),
            $this->getProductImageFacade()
        );

        $productConcreteCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $productConcreteCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $productConcreteCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('ProductConcreteCollectorQuery')
        );

        return $productConcreteCollector;
    }

    /**
     * @return AttributeMapCollector
     */
    public function createAttributeMapCollector(): AttributeMapCollector
    {
        $attributeMapCollector = new AttributeMapCollector(
            $this->getUtilDataReaderService(),
            $this->getProductFacade()
        );

        $attributeMapCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $attributeMapCollector->setQueryBuilder($this->createAttributeMapCollectorQuery());
        $attributeMapCollector->setConfig($this->getConfig());

        return $attributeMapCollector;
    }

    /**
     * @return AttributeMapCollectorQuery
     */
    protected function createAttributeMapCollectorQuery(): AttributeMapCollectorQuery
    {
        return new AttributeMapCollectorQuery();
    }

    /**
     * @return ProductOptionCollector
     */
    public function createStorageProductOptionCollector(): ProductOptionCollector
    {
        $productOptionCollector = new ProductOptionCollector(
            $this->getProductOptionQueryContainer(),
            $this->getUtilDataReaderService(),
            $this->getProductOptionFacade()
        );

        $productOptionCollector->setChunkSize(2);

        $productOptionCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $productOptionCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $productOptionCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('ProductOptionCollectorQuery')
        );

        return $productOptionCollector;
    }

    /**
     * @return AvailabilityCollector
     */
    public function createStorageAvailabilityCollector(): AvailabilityCollector
    {
        $storageAvailabilityCollector = new AvailabilityCollector(
            $this->getUtilDataReaderService()
        );

        $storageAvailabilityCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageAvailabilityCollector->setQueryBuilder(
            $this->createStorageAvailabilityCollectorPropelQuery()
        );
        $storageAvailabilityCollector->setConfig($this->getConfig());

        return $storageAvailabilityCollector;
    }

    /**
     * @return ConcreteTimeSlotCollector
     */
    public function createStorageConcreteTimeSlotCollector(): ConcreteTimeSlotCollector
    {
        $storageConcreteTimeSlotCollector = new ConcreteTimeSlotCollector(
            $this->getUtilDataReaderService()
        );

        $storageConcreteTimeSlotCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $storageConcreteTimeSlotCollector->setQueryBuilder(
            $this->createStorageConcreteTimeSlotCollectorPropelQuery()
        );
        $storageConcreteTimeSlotCollector->setConfig($this->getConfig());

        return $storageConcreteTimeSlotCollector;
    }

    /**
     * @param string $name
     *
     * @return AbstractCollectorQuery
     *@throws \Exception
     *
     */
    public function createStoragePdoQueryAdapterByName($name): AbstractCollectorQuery
    {
        $classList = $this->getConfig()->getStoragePdoQueryAdapterClassNames(
            $this->getCurrentDatabaseEngineName()
        );
        if (!array_key_exists($name, $classList)) {
            throw new Exception('Invalid StoragePdoQueryAdapter name: ' . $name);
        }

        $queryBuilderClassName = $classList[$name];

        return $this->createQueryBuilderByClassName($queryBuilderClassName);
    }

    /**
     * @param string $queryBuilderClassName
     *
     * @return AbstractCollectorQuery
     */
    protected function createQueryBuilderByClassName($queryBuilderClassName): AbstractCollectorQuery
    {
        return new $queryBuilderClassName();
    }

    /**
     * @param string $name
     *
     * @return AbstractCollectorQuery
     *@throws \Exception
     *
     */
    public function createSearchPdoQueryAdapterByName($name): AbstractCollectorQuery
    {
        $classList = $this->getConfig()->getSearchPdoQueryAdapterClassNames(
            $this->getCurrentDatabaseEngineName()
        );
        if (!array_key_exists($name, $classList)) {
            throw new Exception('Invalid SearchPdoQueryAdapter name: ' . $name);
        }

        $queryBuilderClassName = $classList[$name];

        return $this->createQueryBuilderByClassName($queryBuilderClassName);
    }

    /**
     * @return StorageAvailabilityCollectorPropelQuery
     */
    public function createStorageAvailabilityCollectorPropelQuery(): AvailabilityCollectorQuery
    {
        return new StorageAvailabilityCollectorPropelQuery();
    }

    /**
     * @return StorageConcreteTimeSlotCollectorPropelQuery
     */
    public function createStorageConcreteTimeSlotCollectorPropelQuery(): ConcreteTimeSlotCollectorQuery
    {
        return new StorageConcreteTimeSlotCollectorPropelQuery();
    }

    /**
     * @return StorageRedirectCollectorPropelQuery
     */
    public function createStorageRedirectCollectorPropelQuery(): RedirectCollectorQuery
    {
        return new StorageRedirectCollectorPropelQuery();
    }

    /**
     * @return StorageTranslationCollectorPropelQuery
     */
    public function createStorageTranslationCollectorPropelQuery(): TranslationCollectorQuery
    {
        return new StorageTranslationCollectorPropelQuery();
    }

    /**
     * @return CollectorExporter
     */
    public function createTimeSlotSearchExporter(): CollectorExporter
    {
        $searchWriter = $this->createTimeSlotSearchWriter();

        return new CollectorExporter(
            $this->getTouchQueryContainer(),
            $this->getLocaleFacade(),
            $this->createElasticsearchTimeSlotExporter($searchWriter),
            $this->getStoreFacade()
        );
    }

    /**
     * @param WriterInterface $searchWriter
     *
     * @return SearchExporter
     */
    protected function createElasticsearchTimeSlotExporter(WriterInterface $searchWriter): SearchExporter
    {
        $searchExporter = new SearchExporter(
            $this->getTouchQueryContainer(),
            $this->createTimeSlotSearchReader(),
            $searchWriter,
            $this->createTimeSlotSearchMarker(),
            $this->createFailedResultModel(),
            $this->createBatchResultModel(),
            $this->createExporterWriterSearchTouchUpdater()
        );

        foreach ($this->getTimeSlotSearchPlugins() as $touchItemType => $collectorPlugin) {
            $searchExporter->addCollectorPlugin($touchItemType, $collectorPlugin);
        }

        return $searchExporter;
    }

    /**
     * @return ElasticsearchMarkerReader
     */
    protected function createTimeSlotSearchMarkerReader(): ElasticsearchMarkerReader
    {
        return new ElasticsearchMarkerReader(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getTimeSlotSearchIndexName(),
            $this->getConfig()->getTimeSlotSearchDocumentType()
        );
    }

    /**
     * @return ElasticsearchReader
     */
    protected function createTimeSlotSearchReader(): ElasticsearchReader
    {
        return new ElasticsearchReader(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getTimeSlotSearchIndexName(),
            $this->getConfig()->getTimeSlotSearchDocumentType()
        );
    }

    /**
     * @return ElasticsearchMarkerWriter
     */
    protected function createTimeSlotSearchMarkerWriter(): ElasticsearchMarkerWriter
    {
        $elasticsearchMarkerWriter = new ElasticsearchMarkerWriter(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getTimeSlotSearchIndexName(),
            $this->getConfig()->getTimeSlotSearchDocumentType()
        );

        return $elasticsearchMarkerWriter;
    }

    /**
     * @return WriterInterface
     */
    protected function createTimeSlotSearchUpdateWriter(): WriterInterface
    {
        $settings = $this->getConfig();

        $elasticsearchUpdateWriter = new ElasticsearchUpdateWriter(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $settings->getTimeSlotSearchIndexName(),
            $settings->getTimeSlotSearchDocumentType()
        );

        return $elasticsearchUpdateWriter;
    }

    /**
     * @return MarkerInterface
     */
    public function createTimeSlotSearchMarker(): MarkerInterface
    {
        return new ExportMarker(
            $this->createTimeSlotSearchMarkerWriter(),
            $this->createTimeSlotSearchMarkerReader(),
            $this->createSearchMarkerKeyBuilder()
        );
    }

    /**
     *
     * @return array
     */
    protected function getTimeSlotSearchPlugins(): array
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::TIME_SLOT_SEARCH_PLUGINS);
    }

    /**
     * @return ElasticsearchWriter
     */
    protected function createTimeSlotSearchWriter(): ElasticsearchWriter
    {
        $elasticsearchWriter = new ElasticsearchWriter(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getTimeSlotSearchIndexName(),
            $this->getConfig()->getTimeSlotSearchDocumentType()
        );

        return $elasticsearchWriter;
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected function createCriteriaBuilder(): CriteriaBuilderInterface
    {
        $factory = new CriteriaBuilderFactory(
            $this->createCriteriaBuilderContainer()
        );

        $factory->registerWorkerCallback('CriteriaBuilderFactoryWorker', function () use ($factory) {
            return $factory->buildWorker(CriteriaBuilderFactoryWorker::class);
        });

        /** @var CriteriaBuilderFactoryWorker $factoryWorker */
        $factoryWorker = $factory->getWorkerByName('CriteriaBuilderFactoryWorker');

        return $factoryWorker->buildCriteriaBuilder();
    }

    /**
     * @return CriteriaBuilderDependencyContainer
     */
    protected function createCriteriaBuilderContainer(): CriteriaBuilderDependencyContainer
    {
        return new CriteriaBuilderDependencyContainer();
    }

    /**
     * @return \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface
     */
    protected function getCategoryQueryContainer(): CategoryQueryContainerInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_CATEGORY);
    }

    /**
     * @return \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected function getProductCategoryQueryContainer(): ProductCategoryQueryContainerInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_PRODUCT_CATEGORY);
    }

    /**
     * @return ProductImageQueryContainerInterface
     */
    protected function getProductImageQueryContainer(): ProductImageQueryContainerInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_PRODUCT_IMAGE);
    }

    /**
     * @return ProductOptionQueryContainerInterface
     */
    protected function getProductOptionQueryContainer(): ProductOptionQueryContainerInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_PRODUCT_OPTION);
    }

    /**
     * @return SearchFacadeInterface
     */
    protected function getSearchFacade(): SearchFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_SEARCH);
    }

    /**
     * @return PropelFacadeInterface
     */
    protected function getPropelFacade(): PropelFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_PROPEL);
    }

    /**
     * @return \Spryker\Zed\Product\Business\ProductFacadeInterface
     */
    protected function getProductFacade(): ProductFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_PRODUCT);
    }

    /**
     * @return ProductOptionFacadeInterface
     */
    protected function getProductOptionFacade(): ProductOptionFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_PRODUCT_OPTION);
    }

    /**
     * @return string
     */
    protected function getCurrentDatabaseEngineName(): string
    {
        return $this->getPropelFacade()->getCurrentDatabaseEngineName();
    }

    /**
     * @return UtilDataReaderServiceInterface
     */
    protected function getUtilDataReaderService(): UtilDataReaderServiceInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::SERVICE_DATA);
    }

    /**
     * @return ProductImageFacadeInterface
     */
    protected function getProductImageFacade(): ProductImageFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_PRODUCT_IMAGE);
    }

    /**
     * @return PriceProductFacadeInterface
     */
    protected function getPriceProductFacade(): PriceProductFacadeInterface
    {
        return $this->getProvidedDependency(CollectorDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    /**
     * @return DeliveryAreaFacadeInterface
     */
    protected function getDeliveryAreaFacade(): DeliveryAreaFacadeInterface
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return DeliveryAreaQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDeliveryAreaQueryContainer(): DeliveryAreaQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_DELIVERY_AREA);
    }

    /**
     * @return DepositFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDepositFacade(): DepositFacadeInterface
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::FACADE_DEPOSIT);
    }

    /**
     * @return GMTimeSlotCollector
     */
    public function createSearchGMTimeSlotCollector(): GMTimeSlotCollector
    {
        $searchGMTimeSlotCollector = new GMTimeSlotCollector(
            $this->getUtilDataReaderService(),
            $this->getConfig()
        );

        $searchGMTimeSlotCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $searchGMTimeSlotCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $searchGMTimeSlotCollector->setQueryBuilder(
            $this->createGMTimeSlotCollectorQuery()
        );

        return $searchGMTimeSlotCollector;
    }

    /**
     * @return GMTimeSlotCollectorQuery
     */
    protected function createGMTimeSlotCollectorQuery() : GMTimeSlotCollectorQuery
    {
        return new GMTimeSlotCollectorQuery(
            $this->getConfig()
        );
    }

    /**
     * @return CollectorExporter
     */
    public function createGMTimeSlotSearchExporter(): CollectorExporter
    {
        $searchWriter = $this->createGMTimeSlotSearchWriter();

        return new CollectorExporter(
            $this->getTouchQueryContainer(),
            $this->getLocaleFacade(),
            $this->createElasticsearchGMTimeSlotExporter($searchWriter),
            $this->getStoreFacade()
        );
    }

    /**
     * @return ElasticsearchWriter
     */
    protected function createGMTimeSlotSearchWriter(): ElasticsearchWriter
    {
        $elasticsearchWriter = new ElasticsearchWriter(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getGMTimeSlotSearchIndexName(),
            $this->getConfig()->getGMTimeSlotSearchDocumentType()
        );

        return $elasticsearchWriter;
    }

    /**
     * @param WriterInterface $searchWriter
     *
     * @return SearchExporter
     */
    protected function createElasticsearchGMTimeSlotExporter(WriterInterface $searchWriter): SearchExporter
    {
        $searchExporter = new SearchExporter(
            $this->getTouchQueryContainer(),
            $this->createGMTimeSlotSearchReader(),
            $searchWriter,
            $this->createGMTimeSlotSearchMarker(),
            $this->createFailedResultModel(),
            $this->createBatchResultModel(),
            $this->createExporterWriterSearchTouchUpdater()
        );

        foreach ($this->getGMTimeSlotSearchPlugins() as $touchItemType => $collectorPlugin) {
            $searchExporter->addCollectorPlugin($touchItemType, $collectorPlugin);
        }

        return $searchExporter;
    }


    /**
     * @return MarkerInterface
     */
    public function createGMTimeSlotSearchMarker(): MarkerInterface
    {
        return new ExportMarker(
            $this->createGMTimeSlotSearchMarkerWriter(),
            $this->createGMTimeSlotSearchMarkerReader(),
            $this->createSearchMarkerKeyBuilder()
        );
    }

    /**
     *
     * @return array
     */
    protected function getGMTimeSlotSearchPlugins(): array
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::GM_TIME_SLOT_SEARCH_PLUGINS);
    }

    /**
     * @return ElasticsearchMarkerReader
     */
    protected function createGMTimeSlotSearchMarkerReader(): ElasticsearchMarkerReader
    {
        return new ElasticsearchMarkerReader(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getGMTimeSlotSearchIndexName(),
            $this->getConfig()->getGMTimeSlotSearchDocumentType()
        );
    }

    /**
     * @return ElasticsearchReader
     */
    protected function createGMTimeSlotSearchReader(): ElasticsearchReader
    {
        return new ElasticsearchReader(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getGMTimeSlotSearchIndexName(),
            $this->getConfig()->getGMTimeSlotSearchDocumentType()
        );
    }

    /**
     * @return ElasticsearchMarkerWriter
     */
    protected function createGMTimeSlotSearchMarkerWriter(): ElasticsearchMarkerWriter
    {
        $elasticsearchMarkerWriter = new ElasticsearchMarkerWriter(
            StorageInstanceBuilder::getElasticsearchInstance(),
            $this->getConfig()->getGMTimeSlotSearchIndexName(),
            $this->getConfig()->getGMTimeSlotSearchDocumentType()
        );

        return $elasticsearchMarkerWriter;
    }

    /**
     * @return StorageCategoryNodeCollector
     */
    public function createStorageGMSettingsCollector(): GMSettingsCollector
    {
        $gmSettingsCollector = new GMSettingsCollector(
            $this->getUtilDataReaderService(),
            $this->getGraphmastersQueryContainer()
        );

        $gmSettingsCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $gmSettingsCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $gmSettingsCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('GMSettingsCollectorQuery')
        );

        return $gmSettingsCollector;
    }

    /**
     * @return GraphMastersQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getGraphmastersQueryContainer(): GraphMastersQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_GRAPHMASTERS);
    }

    /**
     * @return AbsenceQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getAbsenceQueryContainer(): AbsenceQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(CollectorDependencyProvider::QUERY_CONTAINER_ABSENCE);
    }

    /**
     * @return AbsenceCollector
     */
    public function createStorageAbsenceCollector(): AbsenceCollector
    {
        $absenceCollector = new AbsenceCollector(
            $this->getUtilDataReaderService(),
            $this->getAbsenceQueryContainer()
        );

        $absenceCollector->setTouchQueryContainer(
            $this->getTouchQueryContainer()
        );
        $absenceCollector->setCriteriaBuilder(
            $this->createCriteriaBuilder()
        );
        $absenceCollector->setQueryBuilder(
            $this->createStoragePdoQueryAdapterByName('AbsenceCollectorQuery')
        );

        return $absenceCollector;
    }
}
