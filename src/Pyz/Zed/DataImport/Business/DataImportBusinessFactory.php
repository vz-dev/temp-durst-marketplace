<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\DataImport\Business;

use Pyz\Zed\Category\Business\CategoryFacadeInterface;
use Pyz\Zed\DataImport\Business\Model\Branch\BranchStep;
use Pyz\Zed\DataImport\Business\Model\BranchUser\BranchUserStep;
use Pyz\Zed\DataImport\Business\Model\Category\AddCategoryKeysStep;
use Pyz\Zed\DataImport\Business\Model\Category\Repository\CategoryRepository;
use Pyz\Zed\DataImport\Business\Model\CategoryStyle\CategoryStyleStep;
use Pyz\Zed\DataImport\Business\Model\CategoryTemplate\CategoryTemplateWriterStep;
use Pyz\Zed\DataImport\Business\Model\CmsBlock\CmsBlockWriterStep;
use Pyz\Zed\DataImport\Business\Model\CmsBlockCategory\CmsBlockCategoryWriterStep;
use Pyz\Zed\DataImport\Business\Model\CmsBlockCategoryPosition\CmsBlockCategoryPositionWriterStep;
use Pyz\Zed\DataImport\Business\Model\CmsPage\CmsPageWriterStep;
use Pyz\Zed\DataImport\Business\Model\CmsPage\PlaceholderExtractorStep;
use Pyz\Zed\DataImport\Business\Model\CmsTemplate\CmsTemplateWriterStep;
use Pyz\Zed\DataImport\Business\Model\Country\Repository\CountryRepository;
use Pyz\Zed\DataImport\Business\Model\Country\Repository\CountryRepositoryInterface;
use Pyz\Zed\DataImport\Business\Model\Currency\CurrencyWriterStep;
use Pyz\Zed\DataImport\Business\Model\Customer\CustomerWriterStep;
use Pyz\Zed\DataImport\Business\Model\DataImportStep\LocalizedAttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\DeliveryArea\ConcreteTimeSlotStep;
use Pyz\Zed\DataImport\Business\Model\DeliveryArea\DeliveryAreaStep;
use Pyz\Zed\DataImport\Business\Model\Deposit\DepositSkuStep;
use Pyz\Zed\DataImport\Business\Model\Deposit\DepositStep;
use Pyz\Zed\DataImport\Business\Model\Discount\DiscountWriterStep;
use Pyz\Zed\DataImport\Business\Model\DiscountAmount\DiscountAmountWriterStep;
use Pyz\Zed\DataImport\Business\Model\DiscountVoucher\DiscountVoucherWriterStep;
use Pyz\Zed\DataImport\Business\Model\Driver\DriverWriterStep;
use Pyz\Zed\DataImport\Business\Model\DriverAppRelease\DriverAppReleaseStep;
use Pyz\Zed\DataImport\Business\Model\EnumSalutation\EnumSalutationStep;
use Pyz\Zed\DataImport\Business\Model\Glossary\GlossaryWriterStep;
use Pyz\Zed\DataImport\Business\Model\GraphmastersCommissioningTimes\GraphmastersCommissioningTimesStep;
use Pyz\Zed\DataImport\Business\Model\GraphmastersDeliveryAreaCategory\GraphmastersDeliveryAreaCategoryStep;
use Pyz\Zed\DataImport\Business\Model\GraphmastersOpeningTimes\GraphmastersOpeningTimesStep;
use Pyz\Zed\DataImport\Business\Model\GraphmastersSettings\GraphmastersSettingsStep;
use Pyz\Zed\DataImport\Business\Model\License\LicenseKeyStep;
use Pyz\Zed\DataImport\Business\Model\Locale\AddLocalesStep;
use Pyz\Zed\DataImport\Business\Model\Locale\LocaleNameToIdLocaleStep;
use Pyz\Zed\DataImport\Business\Model\Locale\Repository\LocaleRepository;
use Pyz\Zed\DataImport\Business\Model\Locale\Repository\LocaleRepositoryInterface;
use Pyz\Zed\DataImport\Business\Model\Manufacturer\ManufacturerStep;
use Pyz\Zed\DataImport\Business\Model\Merchant\MerchantStep;
use Pyz\Zed\DataImport\Business\Model\MerchantUser\MerchantUserStep;
use Pyz\Zed\DataImport\Business\Model\Navigation\NavigationKeyToIdNavigationStep;
use Pyz\Zed\DataImport\Business\Model\Navigation\NavigationWriterStep;
use Pyz\Zed\DataImport\Business\Model\NavigationNode\NavigationNodeValidityDatesStep;
use Pyz\Zed\DataImport\Business\Model\NavigationNode\NavigationNodeWriterStep;
use Pyz\Zed\DataImport\Business\Model\PaymentMethod\PaymentMethodStep;
use Pyz\Zed\DataImport\Business\Model\Price\PriceStep;
use Pyz\Zed\DataImport\Business\Model\Product\AttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\Product\ProductLocalizedAttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository;
use Pyz\Zed\DataImport\Business\Model\ProductAbstract\AddProductAbstractSkusStep;
use Pyz\Zed\DataImport\Business\Model\ProductAttributeKey\AddProductAttributeKeysStep;
use Pyz\Zed\DataImport\Business\Model\ProductAttributeKey\ProductAttributeKeyWriter;
use Pyz\Zed\DataImport\Business\Model\ProductGroup\ProductGroupWriter;
use Pyz\Zed\DataImport\Business\Model\ProductLabel\Hook\ProductLabelAfterImportTouchHook;
use Pyz\Zed\DataImport\Business\Model\ProductLabel\ProductLabelWriterStep;
use Pyz\Zed\DataImport\Business\Model\ProductManagementAttribute\ProductManagementAttributeWriter;
use Pyz\Zed\DataImport\Business\Model\ProductManagementAttribute\ProductManagementLocalizedAttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\ProductOption\ProductOptionWriterStep;
use Pyz\Zed\DataImport\Business\Model\ProductOptionPrice\ProductOptionPriceWriterStep;
use Pyz\Zed\DataImport\Business\Model\ProductPrice\ProductPriceWriterStep;
use Pyz\Zed\DataImport\Business\Model\ProductRelation\Hook\ProductRelationAfterImportHook;
use Pyz\Zed\DataImport\Business\Model\ProductRelation\ProductRelationWriter;
use Pyz\Zed\DataImport\Business\Model\ProductReview\ProductReviewWriterStep;
use Pyz\Zed\DataImport\Business\Model\ProductSearchAttribute\Hook\ProductSearchAfterImportHook;
use Pyz\Zed\DataImport\Business\Model\ProductSearchAttribute\ProductSearchAttributeWriter;
use Pyz\Zed\DataImport\Business\Model\ProductSearchAttributeMap\ProductSearchAttributeMapWriter;
use Pyz\Zed\DataImport\Business\Model\ProductSet\ProductSetImageExtractorStep;
use Pyz\Zed\DataImport\Business\Model\ProductStock\ProductStockWriterStep;
use Pyz\Zed\DataImport\Business\Model\Sales\OrdersStep;
use Pyz\Zed\DataImport\Business\Model\Shipment\ShipmentWriterStep;
use Pyz\Zed\DataImport\Business\Model\ShipmentPrice\ShipmentPriceWriterStep;
use Pyz\Zed\DataImport\Business\Model\SoftwareFeature\SoftwareFeatureStep;
use Pyz\Zed\DataImport\Business\Model\SoftwarePackage\SoftwarePackageStep;
use Pyz\Zed\DataImport\Business\Model\Stock\StockWriterStep;
use Pyz\Zed\DataImport\Business\Model\Store\StoreReader;
use Pyz\Zed\DataImport\Business\Model\Store\StoreWriterStep;
use Pyz\Zed\DataImport\Business\Model\Tax\TaxSetNameToIdTaxSetStep;
use Pyz\Zed\DataImport\Business\Model\Tax\TaxWriterStep;
use Pyz\Zed\DataImport\Business\Model\TermsOfService\TermsOfServiceStep;
use Pyz\Zed\DataImport\Business\Model\TimeSlot\TimeSlotStep;
use Pyz\Zed\DataImport\Business\Model\Tour\ConcreteTourStep;
use Pyz\Zed\DataImport\Business\Model\Tour\DrivingLicenseStep;
use Pyz\Zed\DataImport\Business\Model\Tour\TourStep;
use Pyz\Zed\DataImport\Business\Model\VehicleCategory\VehicleCategoryStep;
use Pyz\Zed\DataImport\Business\Model\VehicleType\VehicleTypeStep;
use Pyz\Zed\DataImport\DataImportConfig;
use Pyz\Zed\DataImport\DataImportDependencyProvider;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\ProductSearch\Code\KeyBuilder\FilterGlossaryKeyBuilder;
use Spryker\Zed\Availability\Business\AvailabilityFacadeInterface;
use Spryker\Zed\Cart\Business\CartFacadeInterface;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\DataImport\Business\DataImportBusinessFactory as SprykerDataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImporterAfterImportAwareInterface;
use Spryker\Zed\DataImport\Business\Model\DataImporterBeforeImportAwareInterface;
use Spryker\Zed\DataImport\Business\Model\DataImporterCollectionInterface;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerAwareInterface;
use Spryker\Zed\Discount\DiscountConfig;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\ProductBundle\Business\ProductBundleFacadeInterface;
use Spryker\Zed\ProductRelation\Business\ProductRelationFacadeInterface;

/**
 * @method DataImportConfig getConfig()
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class DataImportBusinessFactory extends SprykerDataImportBusinessFactory
{
    /**
     * @return DataImporterCollectionInterface|DataImporterInterface
     */
    public function getImporter()
    {
        $dataImporterCollection = $this->createDataImporterCollection();
        $dataImporterCollection
            ->addDataImporter($this->createStoreImporter())
            ->addDataImporter($this->createEnumSalutationImporter())
            ->addDataImporter($this->createPaymentMethodImporter())
            ->addDataImporter($this->createMerchantImporter())
            ->addDataImporter($this->createBranchImporter())
            ->addDataImporter($this->createDeliveryAreaImporter())
            ->addDataImporter($this->createCurrencyImporter())
            ->addDataImporter($this->createCategoryTemplateImporter())
            ->addDataImporter($this->createCustomerImporter())
            ->addDataImporter($this->createGlossaryImporter())
            ->addDataImporter($this->createTaxImporter())
            ->addDataImporter($this->createShipmentImporter())
            ->addDataImporter($this->createShipmentPriceImporter())
            ->addDataImporter($this->createDiscountImporter())
            ->addDataImporter($this->createDiscountVoucherImporter())
            ->addDataImporter($this->createStockImporter())
            ->addDataImporter($this->createProductAttributeKeyImporter())
            ->addDataImporter($this->createProductManagementAttributeImporter())
            ->addDataImporter($this->createProductStockImporter())
            ->addDataImporter($this->createProductOptionImporter())
            ->addDataImporter($this->createProductOptionPriceImporter())
            ->addDataImporter($this->createProductGroupImporter())
            ->addDataImporter($this->createProductPriceImporter())
            ->addDataImporter($this->createProductRelationImporter())
            ->addDataImporter($this->createProductReviewImporter())
            ->addDataImporter($this->createProductLabelImporter())
            ->addDataImporter($this->createProductSearchAttributeMapImporter())
            ->addDataImporter($this->createProductSearchAttributeImporter())
            ->addDataImporter($this->createCmsTemplateImporter())
            ->addDataImporter($this->createCmsPageImporter())
            ->addDataImporter($this->createCmsBlockImporter())
            ->addDataImporter($this->createCmsBlockCategoryPositionImporter())
            ->addDataImporter($this->createCmsBlockCategoryImporter())
            ->addDataImporter($this->createNavigationImporter())
            ->addDataImporter($this->createNavigationNodeImporter())
            ->addDataImporter($this->createDiscountAmountImporter())
            ->addDataImporter($this->createDepositImporter())
            ->addDataImporter($this->createPriceImporter())
            ->addDataImporter($this->createTimeSlotImporter())
            ->addDataImporter($this->createCategoryStyleImporter())
            ->addDataImporter($this->createTermsOfServiceImporter())
            ->addDataImporter($this->createManufacturerImporter())
            ->addDataImporter($this->createSoftwarePackageImporter())
            ->addDataImporter($this->createSoftwareFeatureImporter())
            ->addDataImporter($this->createVehicleTypeImporter())
            ->addDataImporter($this->createVehicleCategoryImporter())
            ->addDataImporter($this->createLicenseKeyImporter())
            ->addDataImporter($this->createTourImporter())
            ->addDataImporter($this->createDepositSkuImporter())
            ->addDataImporter($this->createDrivingLicenceImporter())
            ->addDataImporter($this->createDriverImporter())
            ->addDataImporter($this->createConcreteTourImporter())
            ->addDataImporter($this->createConcreteTimeSlotImporter())
            ->addDataImporter($this->createOrdersImporter())
            ->addDataImporter($this->createDriverAppReleaseImporter())
            ->addDataImporter($this->createBranchUserImporter())
            ->addDataImporter($this->createGraphmastersSettingsImporter())
            ->addDataImporter($this->createGraphmastersDeliveryAreaCategoryImporter())
            ->addDataImporter($this->createGraphmastersOpeningTimesImporter())
            ->addDataImporter($this->createGraphmastersCommissioningTimesImporter())
            ->addDataImporter($this->createMerchantUserImporter());

        return $dataImporterCollection;
    }

    /**
     * @return DataImporterInterface
     */
    protected function createCurrencyImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCurrencyDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new CurrencyWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface
     */
    protected function createStoreImporter()
    {
        $dataImporter = $this->createDataImporter(
            $this->getConfig()->getStoreDataImporterConfiguration()->getImportType(),
            new StoreReader(
                $this->createDataSet(
                    Store::getInstance()->getAllowedStores()
                )
            )
        );

        $dataSetStepBroker = $this->createDataSetStepBroker();
        $dataSetStepBroker->addStep(new StoreWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface
     */
    protected function createGlossaryImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getGlossaryDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(GlossaryWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep($this->createLocaleNameToIdStep(GlossaryWriterStep::KEY_LOCALE))
            ->addStep(new GlossaryWriterStep($this->getTouchFacade(), GlossaryWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface
     */
    protected function createCategoryTemplateImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCategoryTemplateDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new CategoryTemplateWriterStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return CategoryRepository
     */
    protected function createCategoryRepository()
    {
        return new CategoryRepository();
    }

    /**
     * @return CategoryFacadeInterface
     */
    protected function getCategoryFacade()
    {
        return $this->getProvidedDependency(DataImportDependencyProvider::FACADE_CATEGORY);
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCustomerImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCustomerDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new CustomerWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCmsTemplateImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCmsTemplateDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new CmsTemplateWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCmsPageImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCmsPageDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(CmsPageWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createPlaceholderExtractorStep([
                CmsPageWriterStep::KEY_PLACEHOLDER_TITLE,
                CmsPageWriterStep::KEY_PLACEHOLDER_CONTENT,
            ]))
            ->addStep($this->createLocalizedAttributesExtractorStep([
                CmsPageWriterStep::KEY_URL,
                CmsPageWriterStep::KEY_NAME,
                CmsPageWriterStep::KEY_META_TITLE,
                CmsPageWriterStep::KEY_META_DESCRIPTION,
                CmsPageWriterStep::KEY_META_KEYWORDS,
            ]))
            ->addStep(new CmsPageWriterStep($this->getTouchFacade(), CmsPageWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCmsBlockImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCmsBlockDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(CmsBlockWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createLocalizedAttributesExtractorStep([
                CmsBlockWriterStep::KEY_PLACEHOLDER_TITLE,
                CmsBlockWriterStep::KEY_PLACEHOLDER_DESCRIPTION,
            ]))
            ->addStep(new CmsBlockWriterStep(
                $this->createCategoryRepository(),
                $this->createProductRepository(),
                $this->getTouchFacade(),
                CmsBlockWriterStep::BULK_SIZE
            ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCmsBlockCategoryPositionImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCmsBlockCategoryPositionDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new CmsBlockCategoryPositionWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCmsBlockCategoryImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCmsBlockCategoryDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new CmsBlockCategoryWriterStep(
                $this->getTouchFacade()
            ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @param array|null $defaultPlaceholder
     *
     * @return PlaceholderExtractorStep
     */
    protected function createPlaceholderExtractorStep(array $defaultPlaceholder = [])
    {
        return new PlaceholderExtractorStep($defaultPlaceholder);
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDiscountImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getDiscountDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(DiscountWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new DiscountWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDiscountAmountImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getDiscountAmountDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(DiscountAmountWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new DiscountAmountWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDiscountVoucherImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getDiscountVoucherDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(DiscountVoucherWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new DiscountVoucherWriterStep($this->createDiscountConfig()));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DiscountConfig
     */
    protected function createDiscountConfig()
    {
        return new DiscountConfig();
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductPriceImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductPriceDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(ProductPriceWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new ProductPriceWriterStep($this->createProductRepository()));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductOptionImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductOptionDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createTaxSetNameToIdTaxSetStep(ProductOptionWriterStep::KEY_TAX_SET_NAME))
            ->addStep($this->createLocalizedAttributesExtractorStep([
                ProductOptionWriterStep::KEY_GROUP_NAME,
                ProductOptionWriterStep::KEY_OPTION_NAME,
            ]))
            ->addStep(new ProductOptionWriterStep($this->getTouchFacade(), ProductOptionWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductOptionPriceImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductOptionPriceDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new ProductOptionPriceWriterStep($this->getTouchFacade(), ProductOptionPriceWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductStockImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductStockDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(ProductStockWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new ProductStockWriterStep(
                $this->createProductRepository(),
                $this->getAvailabilityFacade(),
                $this->getProductBundleFacade(),
                $this->getTouchFacade(),
                ProductStockWriterStep::BULK_SIZE
            ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return AvailabilityFacadeInterface
     */
    protected function getAvailabilityFacade()
    {
        return $this->getProvidedDependency(DataImportDependencyProvider::FACADE_AVAILABILITY);
    }

    /**
     * @return ProductBundleFacadeInterface
     */
    protected function getProductBundleFacade()
    {
        return $this->getProvidedDependency(DataImportDependencyProvider::FACADE_PRODUCT_BUNDLE);
    }

    /**
     * @return LocaleRepositoryInterface
     */
    protected function createLocaleRepository()
    {
        return new LocaleRepository();
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createStockImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getStockDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new StockWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createShipmentImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getShipmentDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(ShipmentWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep($this->createTaxSetNameToIdTaxSetStep())
            ->addStep(new ShipmentWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createShipmentPriceImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getShipmentPriceDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(ShipmentWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new ShipmentPriceWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createTaxImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getTaxDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(TaxWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new TaxWriterStep($this->createCountryRepository()));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return CountryRepositoryInterface
     */
    protected function createCountryRepository()
    {
        return new CountryRepository();
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createNavigationImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getNavigationDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(NavigationWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new NavigationWriterStep($this->getTouchFacade(), NavigationWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createNavigationNodeImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getNavigationNodeDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(NavigationNodeWriterStep::BULK_SIZE);
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createNavigationKeyToIdNavigationStep(NavigationNodeWriterStep::KEY_NAVIGATION_KEY))
            ->addStep($this->createLocalizedAttributesExtractorStep([
                NavigationNodeWriterStep::KEY_TITLE,
                NavigationNodeWriterStep::KEY_URL,
                NavigationNodeWriterStep::KEY_CSS_CLASS,
            ]))
            ->addStep($this->createNavigationNodeValidityDatesStep(NavigationNodeWriterStep::KEY_VALID_FROM, NavigationNodeWriterStep::KEY_VALID_TO))
            ->addStep(new NavigationNodeWriterStep($this->getTouchFacade(), NavigationNodeWriterStep::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return NavigationKeyToIdNavigationStep
     */
    protected function createNavigationKeyToIdNavigationStep($source = NavigationKeyToIdNavigationStep::KEY_SOURCE, $target = NavigationKeyToIdNavigationStep::KEY_TARGET)
    {
        return new NavigationKeyToIdNavigationStep($source, $target);
    }

    /**
     * @param string $keyValidFrom
     * @param string $keyValidTo
     *
     * @return NavigationNodeValidityDatesStep
     */
    protected function createNavigationNodeValidityDatesStep($keyValidFrom, $keyValidTo)
    {
        return new NavigationNodeValidityDatesStep($keyValidFrom, $keyValidTo);
    }

    /**
     * @return ProductRepository
     */
    protected function createProductRepository()
    {
        return new ProductRepository();
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductAttributeKeyImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductAttributeKeyDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new ProductAttributeKeyWriter());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductManagementAttributeImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductManagementAttributeDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createAddProductAttributeKeysStep())
            ->addStep($this->createProductManagementLocalizedAttributesExtractorStep())
            ->addStep(new ProductManagementAttributeWriter($this->getTouchFacade(), ProductManagementAttributeWriter::BULK_SIZE));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return ProductManagementLocalizedAttributesExtractorStep
     */
    protected function createProductManagementLocalizedAttributesExtractorStep()
    {
        return new ProductManagementLocalizedAttributesExtractorStep();
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductGroupImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductGroupDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker(ProductGroupWriter::BULK_SIZE);
        $dataSetStepBroker
            ->addStep(new ProductGroupWriter(
                $this->createProductRepository(),
                $this->getTouchFacade(),
                ProductGroupWriter::BULK_SIZE
            ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductRelationImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductRelationDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddProductAbstractSkusStep())
            ->addStep(new ProductRelationWriter());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);
        $dataImporter->addAfterImportHook($this->createProductRelationAfterImportHook());

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductReviewImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductReviewDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new ProductReviewWriterStep(
            $this->createProductRepository(),
            $this->createLocaleRepository(),
            $this->getTouchFacade()
        ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductLabelImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductLabelDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddProductAbstractSkusStep())
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createLocalizedAttributesExtractorStep(['name']))
            ->addStep(new ProductLabelWriterStep(
                $this->getTouchFacade(),
                ProductLabelWriterStep::BULK_SIZE
            ));

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker)
            ->addAfterImportHook($this->createProductLabelAfterImportTouchHook());

        return $dataImporter;
    }

    /**
     * @return ProductLabelAfterImportTouchHook
     */
    protected function createProductLabelAfterImportTouchHook()
    {
        return new ProductLabelAfterImportTouchHook(
            $this->getTouchFacade()
        );
    }

    /**
     * @return ProductRelationAfterImportHook
     */
    protected function createProductRelationAfterImportHook()
    {
        return new ProductRelationAfterImportHook(
            $this->getProductRelationFacade()
        );
    }

    /**
     * @return ProductSetImageExtractorStep
     */
    protected function createProductSetImageExtractorStep()
    {
        return new ProductSetImageExtractorStep();
    }

    /**
     * @return ProductRelationFacadeInterface
     */
    protected function getProductRelationFacade()
    {
        return $this->getProvidedDependency(DataImportDependencyProvider::FACADE_PRODUCT_RELATION);
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductSearchAttributeMapImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductSearchAttributeMapDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddProductAttributeKeysStep())
            ->addStep(new ProductSearchAttributeMapWriter());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createProductSearchAttributeImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getProductSearchAttributeDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createAddLocalesStep())
            ->addStep($this->createAddProductAttributeKeysStep())
            ->addStep($this->createLocalizedAttributesExtractorStep([ProductSearchAttributeWriter::KEY]))
            ->addStep(new ProductSearchAttributeWriter(
                $this->createSearchGlossaryKeyBuilder(),
                $this->getTouchFacade(),
                ProductSearchAttributeWriter::BULK_SIZE
            ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);
        $dataImporter->addAfterImportHook($this->createProductSearchAfterImportHook());

        return $dataImporter;
    }

    /**
     * @return ProductSearchAfterImportHook
     */
    protected function createProductSearchAfterImportHook()
    {
        return new ProductSearchAfterImportHook($this->getProvidedDependency(DataImportDependencyProvider::FACADE_PRODUCT_SEARCH));
    }

    /**
     * @return FilterGlossaryKeyBuilder
     */
    protected function createSearchGlossaryKeyBuilder()
    {
        return new FilterGlossaryKeyBuilder();
    }

    /**
     * @return AddLocalesStep
     */
    protected function createAddLocalesStep()
    {
        return new AddLocalesStep($this->getStore());
    }

    /**
     * @return Store
     */
    protected function getStore()
    {
        return $this->getProvidedDependency(DataImportDependencyProvider::STORE);
    }

    /**
     * @return AddCategoryKeysStep
     */
    protected function createAddCategoryKeysStep()
    {
        return new AddCategoryKeysStep();
    }

    /**
     * @return AttributesExtractorStep
     */
    protected function createAttributesExtractorStep()
    {
        return new AttributesExtractorStep();
    }

    /**
     * @param array $defaultAttributes
     *
     * @return LocalizedAttributesExtractorStep
     */
    protected function createLocalizedAttributesExtractorStep(array $defaultAttributes = [])
    {
        return new LocalizedAttributesExtractorStep($defaultAttributes);
    }

    /**
     * @param array $defaultAttributes
     *
     * @return ProductLocalizedAttributesExtractorStep
     */
    protected function createProductLocalizedAttributesExtractorStep(array $defaultAttributes = [])
    {
        return new ProductLocalizedAttributesExtractorStep($defaultAttributes);
    }

    /**
     * @return AddProductAbstractSkusStep
     */
    protected function createAddProductAbstractSkusStep()
    {
        return new AddProductAbstractSkusStep();
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return LocaleNameToIdLocaleStep
     */
    protected function createLocaleNameToIdStep($source = LocaleNameToIdLocaleStep::KEY_SOURCE, $target = LocaleNameToIdLocaleStep::KEY_TARGET)
    {
        return new LocaleNameToIdLocaleStep($source, $target);
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return TaxSetNameToIdTaxSetStep
     */
    protected function createTaxSetNameToIdTaxSetStep($source = TaxSetNameToIdTaxSetStep::KEY_SOURCE, $target = TaxSetNameToIdTaxSetStep::KEY_TARGET)
    {
        return new TaxSetNameToIdTaxSetStep($source, $target);
    }

    /**
     * @return AddProductAttributeKeysStep
     */
    protected function createAddProductAttributeKeysStep()
    {
        return new AddProductAttributeKeysStep();
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createEnumSalutationImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getEnumSalutationImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new EnumSalutationStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createMerchantImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getMerchantImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new MerchantStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createBranchImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getBranchImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new BranchStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDeliveryAreaImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getDeliveryAreaImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new DeliveryAreaStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDepositImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getDepositImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new DepositStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createPaymentMethodImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getPaymentMethodImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new PaymentMethodStep(
            $this->getTouchFacade()
        ));

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createPriceImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getPriceImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new PriceStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createTimeSlotImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getTimeSlotImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new TimeSlotStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createCategoryStyleImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getCategoryStyleImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new CategoryStyleStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createTermsOfServiceImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getTermsOfServiceImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new TermsOfServiceStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createManufacturerImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getManufacturerImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new ManufacturerStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createSoftwarePackageImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getSoftwarePackageImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new SoftwarePackageStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createSoftwareFeatureImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getSoftwareFeatureImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new SoftwareFeatureStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createVehicleTypeImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getVehicleTypeImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new VehicleTypeStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createVehicleCategoryImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getVehicleCategoryImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new VehicleCategoryStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createLicenseKeyImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                ->getConfig()
                ->getLicenseKeyImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new LicenseKeyStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createTourImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getTourImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new TourStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDepositSkuImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getDepositSkuImporterConfig()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new DepositSkuStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDrivingLicenceImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getDrivingLicenceImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new DrivingLicenseStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDriverImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getDriverImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new DriverWriterStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createConcreteTourImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getConcreteTourImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new ConcreteTourStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createConcreteTimeSlotImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getConcreteTimeSlotImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new ConcreteTimeSlotStep());

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createOrdersImporter()
    {
        $dataImporter = $this
            ->getCsvDataImporterFromConfig(
                $this
                    ->getConfig()
                    ->getOrdersImporterConfiguration()
            );

        $dataSetStepBroker = $this
            ->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep(new OrdersStep(
                $this->getHeidelpayRestFacade(),
                $this->getCartFacade(),
                $this->getCheckoutFacade(),
                $this->getCurrencyFacade(),
                $this->getConfig()
            ));

        $dataImporter
            ->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return HeidelpayRestFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getHeidelpayRestFacade(): HeidelpayRestFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DataImportDependencyProvider::FACADE_HEIDELPAY_REST
            );
    }

    /**
     * @return CartFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getCartFacade(): CartFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DataImportDependencyProvider::FACADE_CART
            );
    }

    /**
     * @return CheckoutFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getCheckoutFacade(): CheckoutFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DataImportDependencyProvider::FACADE_CHECKOUT
            );
    }

    /**
     * @return CurrencyFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getCurrencyFacade(): CurrencyFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DataImportDependencyProvider::FACADE_CURRENCY
            );
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createDriverAppReleaseImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getDriverAppReleaseImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new DriverAppReleaseStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createBranchUserImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getBranchUserImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new BranchUserStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createGraphmastersSettingsImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getGraphmastersSettingsImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new GraphmastersSettingsStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createGraphmastersDeliveryAreaCategoryImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getGraphmastersDeliveryAreaCategoryImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new GraphmastersDeliveryAreaCategoryStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createGraphmastersOpeningTimesImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getGraphmastersOpeningTimesImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new GraphmastersOpeningTimesStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createGraphmastersCommissioningTimesImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getGraphmastersCommissioningTimesImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new GraphmastersCommissioningTimesStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return DataImporterAfterImportAwareInterface|DataImporterBeforeImportAwareInterface|DataImporterInterface|DataSetStepBrokerAwareInterface
     */
    protected function createMerchantUserImporter()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getMerchantUserImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep(new MerchantUserStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }
}
