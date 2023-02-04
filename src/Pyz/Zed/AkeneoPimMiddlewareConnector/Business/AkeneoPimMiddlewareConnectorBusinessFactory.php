<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - AkeneoPimMiddlewareConnectorBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.03.18
 * Time: 09:52
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business;

use Pyz\Zed\AkeneoPimMiddlewareConnector\AkeneoPimMiddlewareConnectorDependencyProvider;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\Importer;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map\AttributeMapImportMap;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map\ProductImportMap;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\ManufacturerRepository;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\UnitRepository;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Translator\Dictionary\ProductImportDictionary;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Writer\ProductAbstractWriterStep;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Writer\ProductConcreteWriter;
use Pyz\Zed\DataImport\Business\Model\Category\AddCategoryKeysStep;
use Pyz\Zed\DataImport\Business\Model\Category\CategoryWriterStep;
use Pyz\Zed\DataImport\Business\Model\Category\Repository\CategoryRepository;
use Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository;
use Pyz\Zed\DataImport\Business\Model\ProductAttributeKey\AddProductAttributeKeysStep;
use Pyz\Zed\DataImport\Business\Model\ProductAttributeKey\ProductAttributeKeyWriter;
use Pyz\Zed\DataImport\Business\Model\ProductManagementAttribute\ProductManagementAttributeWriter;
use Pyz\Zed\DataImport\Business\Model\Tax\TaxSetNameToIdTaxSetStep;
use Spryker\Zed\Availability\Business\AvailabilityFacadeInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSet;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBroker;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\AkeneoPimMiddlewareConnectorBusinessFactory as SprykerEcoAkeneoPimMiddlewareConnectorBusinessFactory;
use SprykerMiddleware\Zed\Process\Business\Mapper\Map\MapInterface;
use SprykerMiddleware\Zed\Process\Business\Translator\Dictionary\DictionaryInterface;

class AkeneoPimMiddlewareConnectorBusinessFactory extends SprykerEcoAkeneoPimMiddlewareConnectorBusinessFactory
{
    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\Importer
     */
    public function createCategoryImporter()
    {
        return new Importer(
            $this->createCategoryImportDataSetStepBroker(),
            $this->createDataSet()
        );
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerInterface
     */
    protected function createCategoryImportDataSetStepBroker()
    {
        $dataSetStepBroker = new DataSetStepBroker();
        $dataSetStepBroker->addStep($this->createCategoryWriteStep());
        return $dataSetStepBroker;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerInterface
     */
    protected function createAttributeImportDataSetStepBroker()
    {
        $dataSetStepBroker = new DataSetStepBroker();
        $dataSetStepBroker->addStep(new AddProductAttributeKeysStep());
        $dataSetStepBroker->addStep(new ProductManagementAttributeWriter($this->getTouchFacade()));
        return $dataSetStepBroker;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerInterface
     */
    protected function createAttributeKeysDataSetStepBroker()
    {
        $dataSetStepBroker = new DataSetStepBroker();
        $dataSetStepBroker->addStep($this->createProductAttributeKeyWriter());
        return $dataSetStepBroker;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBroker
     */
    protected function createProductAbstractImportDataSetStepBroker()
    {
        $dataSetStepBroker = new DataSetStepBroker();
        $dataSetStepBroker->addStep(new AddCategoryKeysStep());
        $dataSetStepBroker->addStep(new TaxSetNameToIdTaxSetStep(TaxSetNameToIdTaxSetStep::KEY_SOURCE, TaxSetNameToIdTaxSetStep::KEY_TARGET));
        $dataSetStepBroker->addStep(new ProductAbstractWriterStep(
            new ProductRepository(),
            $this->getTouchFacade(),
            null,
            $this->createManufacturerRepository()
        ));
        return $dataSetStepBroker;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBroker
     */
    protected function createProductConcreteImportDataSetStepBroker()
    {
        $dataSetStepBroker = new DataSetStepBroker();
        $dataSetStepBroker->addStep(new ProductConcreteWriter(
            new ProductRepository(),
            $this->getTouchFacade(),
            null,
            $this->createUnitRepository(),
            $this->getAvailabilityFacade()
        ));
        return $dataSetStepBroker;
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\ManufacturerRepository
     */
    protected function createManufacturerRepository(): ManufacturerRepository
    {
        return new ManufacturerRepository();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface
     */
    protected function createDataSet()
    {
        return new DataSet();
    }

    /**
     * @return \Spryker\Zed\DataImport\Dependency\Facade\DataImportToTouchInterface
     */
    protected function getTouchFacade()
    {
        return $this
            ->getProvidedDependency(AkeneoPimMiddlewareConnectorDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return \Pyz\Zed\DataImport\Business\Model\Category\CategoryWriterStep
     */
    protected function createCategoryWriteStep()
    {
        return new CategoryWriterStep(
            $this->createCategoryRepository(),
            $this->getTouchFacade()
        );
    }

    /**
     * @return \Pyz\Zed\DataImport\Business\Model\Category\Repository\CategoryRepositoryInterface
     */
    protected function createCategoryRepository()
    {
        return new CategoryRepository();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createProductAttributeKeyWriter()
    {
        return new ProductAttributeKeyWriter();
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\Importer
     */
    public function createAttributeImporter()
    {
        return new Importer(
            $this->createAttributeImportDataSetStepBroker(),
            $this->createDataSet()
        );
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\Importer
     */
    public function createProductConcreteImporter()
    {
        return new Importer(
            $this->createProductConcreteImportDataSetStepBroker(),
            $this->createDataSet()
        );
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\Importer
     */
    public function createProductAbstractImporter()
    {
        return new Importer(
            $this->createProductAbstractImportDataSetStepBroker(),
            $this->createDataSet()
        );
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer\ImporterInterface
     */
    public function createAttributeKeyImporter()
    {
        return new Importer(
            $this->createAttributeKeysDataSetStepBroker(),
            $this->createDataSet()
        );
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Business\Mapper\Map\MapInterface
     */
    public function createProductImportMap(): MapInterface
    {
        return new ProductImportMap(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Business\Translator\Dictionary\DictionaryInterface
     */
    public function createProductImportDictionary(): DictionaryInterface
    {
        return new ProductImportDictionary($this->getConfig());
    }

    /**
     * @return \Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\UnitRepository
     */
    protected function createUnitRepository(): UnitRepository
    {
        return new UnitRepository();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Business\Mapper\Map\MapInterface
     */
    public function createAttributeMapImportMap(): MapInterface
    {
        return new AttributeMapImportMap();
    }

    /**
     * @return \Spryker\Zed\Availability\Business\AvailabilityFacadeInterface
     */
    protected function getAvailabilityFacade(): AvailabilityFacadeInterface
    {
        return $this
            ->getProvidedDependency(AkeneoPimMiddlewareConnectorDependencyProvider::FACADE_AVAILABILITY);
    }
}
