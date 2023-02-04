<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\DataImport;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReaderConfigurationTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\DataImport\DataImportConfig as SprykerDataImportConfig;

class DataImportConfig extends SprykerDataImportConfig
{
    public const IMPORT_TYPE_CATEGORY_TEMPLATE = 'category-template';
    public const IMPORT_TYPE_CUSTOMER = 'customer';
    public const IMPORT_TYPE_GLOSSARY = 'glossary';
    public const IMPORT_TYPE_NAVIGATION = 'navigation';
    public const IMPORT_TYPE_NAVIGATION_NODE = 'navigation-node';
    public const IMPORT_TYPE_PRODUCT_PRICE = 'product-price';
    public const IMPORT_TYPE_PRODUCT_STOCK = 'product-stock';
    public const IMPORT_TYPE_PRODUCT_ATTRIBUTE_KEY = 'product-attribute-key';
    public const IMPORT_TYPE_PRODUCT_MANAGEMENT_ATTRIBUTE = 'product-management-attribute';
    public const IMPORT_TYPE_PRODUCT_RELATION = 'product-relation';
    public const IMPORT_TYPE_PRODUCT_REVIEW = 'product-review';
    public const IMPORT_TYPE_PRODUCT_LABEL = 'product-label';
    public const IMPORT_TYPE_PRODUCT_GROUP = 'product-group';
    public const IMPORT_TYPE_PRODUCT_OPTION = 'product-option';
    public const IMPORT_TYPE_PRODUCT_OPTION_PRICE = 'product-option-price';
    public const IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE_MAP = 'product-search-attribute-map';
    public const IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE = 'product-search-attribute';
    public const IMPORT_TYPE_CMS_TEMPLATE = 'cms-template';
    public const IMPORT_TYPE_CMS_PAGE = 'cms-page';
    public const IMPORT_TYPE_CMS_BLOCK = 'cms-block';
    public const IMPORT_TYPE_CMS_BLOCK_CATEGORY_POSITION = 'cms-block-category-position';
    public const IMPORT_TYPE_CMS_BLOCK_CATEGORY = 'cms-block-category';
    public const IMPORT_TYPE_DISCOUNT = 'discount';
    public const IMPORT_TYPE_DISCOUNT_AMOUNT = 'discount-amount';
    public const IMPORT_TYPE_DISCOUNT_VOUCHER = 'discount-voucher';
    public const IMPORT_TYPE_SHIPMENT = 'shipment';
    public const IMPORT_TYPE_SHIPMENT_PRICE = 'shipment-price';
    public const IMPORT_TYPE_STOCK = 'stock';
    public const IMPORT_TYPE_TAX = 'tax';
    public const IMPORT_TYPE_CURRENCY = 'currency';
    public const IMPORT_TYPE_STORE = 'store';
    public const IMPORT_TYPE_SOFTWARE_PACKAGE = 'software-package';
    public const IMPORT_TYPE_VEHICLE_TYPE = 'vehicle-type';
    public const IMPORT_TYPE_VEHICLE_CATEGORY = 'vehicle-category';
    public const IMPORT_TYPE_ENUM_SALUTATION = 'enum-salutation';
    public const IMPORT_TYPE_MERCHANT = 'merchant';
    public const IMPORT_TYPE_BRANCH = 'branch';
    public const IMPORT_TYPE_DELIVERY_AREA = 'delivery-area';
    public const IMPORT_TYPE_DEPOSIT = 'deposit';
    public const IMPORT_TYPE_PAYMENT_METHOD = 'payment-method';
    public const IMPORT_TYPE_PRICE = 'price';
    public const IMPORT_TYPE_TIME_SLOT = 'time-slot';
    public const IMPORT_TYPE_CATEGORY_STYLE = 'category-style';
    public const IMPORT_TYPE_TERMS_OF_SERVICE = 'terms-of-service';
    public const IMPORT_TYPE_MANUFACTURER = 'manufacturer';
    public const IMPORT_TYPE_SOFTWARE_FEATURE = 'software-feature';
    public const IMPORT_TYPE_LICENSE_KEY = 'license-key';
    public const IMPORT_TYPE_TOUR = 'tour';
    public const IMPORT_TYPE_DEPOSIT_SKU = 'deposit-sku';
    public const IMPORT_TYPE_DRIVING_LICENCE = 'driving-license';
    public const IMPORT_TYPE_DRIVER = 'driver';
    public const IMPORT_TYPE_CONCRETE_TOUR = 'concrete-tour';
    public const IMPORT_TYPE_CONCRETE_TIME_SLOT = 'concrete-time-slot';
    public const IMPORT_TYPE_ORDERS = 'orders';
    public const IMPORT_TYPE_DRIVER_APP_RELEASE = 'driver-app-release';
    public const IMPORT_TYPE_BRANCH_USER = 'branch-user';
    public const IMPORT_TYPE_GRAPHMASTERS_SETTINGS = 'graphmasters-settings';
    public const IMPORT_TYPE_GRAPHMASTERS_DELIVERY_AREA_CATEGORY = 'graphmasters-delivery-area-category';
    public const IMPORT_TYPE_GRAPHMASTERS_OPENING_TIMES = 'graphmasters-opening-times';
    public const IMPORT_TYPE_GRAPHMASTERS_COMMISSIONING_TIMES = 'graphmasters-commissioning-times';
    public const IMPORT_TYPE_MERCHANT_USER = 'merchant-user';

    /**
     * @return string
     */
    public function getDateTimeFormat(): string
    {
        return $this
            ->get(
                DeliveryAreaConstants::TIME_SLOT_DATE_TIME_FORMAT
            );
    }

    /**
     * @return string
     */
    public function getProjectTimezone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCurrencyDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('currency.csv', static::IMPORT_TYPE_CURRENCY);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getStoreDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('', static::IMPORT_TYPE_STORE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getGlossaryDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('glossary.csv', static::IMPORT_TYPE_GLOSSARY);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCustomerDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('customer.csv', static::IMPORT_TYPE_CUSTOMER);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCategoryTemplateDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('category_template.csv', static::IMPORT_TYPE_CATEGORY_TEMPLATE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getTaxDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('tax.csv', static::IMPORT_TYPE_TAX);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductPriceDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_price.csv', static::IMPORT_TYPE_PRODUCT_PRICE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductStockDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_stock.csv', static::IMPORT_TYPE_PRODUCT_STOCK);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getStockDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('stock.csv', static::IMPORT_TYPE_STOCK);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getShipmentDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('shipment.csv', static::IMPORT_TYPE_SHIPMENT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getShipmentPriceDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('shipment_price.csv', static::IMPORT_TYPE_SHIPMENT_PRICE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getNavigationDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('navigation.csv', static::IMPORT_TYPE_NAVIGATION);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getNavigationNodeDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('navigation_node.csv', static::IMPORT_TYPE_NAVIGATION_NODE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductAttributeKeyDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_attribute_key.csv', static::IMPORT_TYPE_PRODUCT_ATTRIBUTE_KEY);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductManagementAttributeDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_management_attribute.csv', static::IMPORT_TYPE_PRODUCT_MANAGEMENT_ATTRIBUTE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductRelationDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_relation.csv', static::IMPORT_TYPE_PRODUCT_RELATION);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductReviewDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_review.csv', static::IMPORT_TYPE_PRODUCT_REVIEW);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductLabelDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_label.csv', static::IMPORT_TYPE_PRODUCT_LABEL);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductSearchAttributeMapDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_search_attribute_map.csv', static::IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE_MAP);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductSearchAttributeDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_search_attribute.csv', static::IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductGroupDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_group.csv', static::IMPORT_TYPE_PRODUCT_GROUP);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductOptionDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_option.csv', static::IMPORT_TYPE_PRODUCT_OPTION);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getProductOptionPriceDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('product_option_price.csv', static::IMPORT_TYPE_PRODUCT_OPTION_PRICE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCmsTemplateDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('cms_template.csv', static::IMPORT_TYPE_CMS_TEMPLATE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCmsPageDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('cms_page.csv', static::IMPORT_TYPE_CMS_PAGE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCmsBlockDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('cms_block.csv', static::IMPORT_TYPE_CMS_BLOCK);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCmsBlockCategoryPositionDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('cms_block_category_position.csv', static::IMPORT_TYPE_CMS_BLOCK_CATEGORY_POSITION);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCmsBlockCategoryDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('cms_block_category.csv', static::IMPORT_TYPE_CMS_BLOCK_CATEGORY);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDiscountDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('discount.csv', static::IMPORT_TYPE_DISCOUNT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDiscountAmountDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('discount_amount.csv', static::IMPORT_TYPE_DISCOUNT_AMOUNT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDiscountVoucherDataImporterConfiguration()
    {
        return $this->buildImporterConfiguration('discount_voucher.csv', static::IMPORT_TYPE_DISCOUNT_VOUCHER);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getEnumSalutationImporterConfiguration()
    {
        return $this->buildImporterConfiguration('enum_salutations.csv', static::IMPORT_TYPE_ENUM_SALUTATION);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getMerchantImporterConfiguration()
    {
        return $this->buildImporterConfiguration('merchants.csv', static::IMPORT_TYPE_MERCHANT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getBranchImporterConfiguration()
    {
        return $this->buildImporterConfiguration('branches.csv', static::IMPORT_TYPE_BRANCH);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDeliveryAreaImporterConfiguration()
    {
        return $this->buildImporterConfiguration('delivery_areas.csv', static::IMPORT_TYPE_DELIVERY_AREA);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDepositImporterConfiguration()
    {
        return $this->buildImporterConfiguration('deposit.csv', static::IMPORT_TYPE_DEPOSIT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getPaymentMethodImporterConfiguration()
    {
        return $this->buildImporterConfiguration('payment_methods.csv', static::IMPORT_TYPE_PAYMENT_METHOD);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getPriceImporterConfiguration()
    {
        return $this->buildImporterConfiguration('price.csv', static::IMPORT_TYPE_PRICE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getCategoryStyleImporterConfiguration()
    {
        return $this->buildImporterConfiguration('category_style.csv', static::IMPORT_TYPE_CATEGORY_STYLE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getTimeSlotImporterConfiguration()
    {
        return $this->buildImporterConfiguration('time_slots.csv', static::IMPORT_TYPE_TIME_SLOT);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getTermsOfServiceImporterConfiguration()
    {
        return $this->buildImporterConfiguration('terms_of_service.csv', static::IMPORT_TYPE_TERMS_OF_SERVICE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getManufacturerImporterConfiguration()
    {
        return $this->buildImporterConfiguration('manufacturer.csv', static::IMPORT_TYPE_MANUFACTURER);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getSoftwarePackageImporterConfiguration()
    {
        return $this->buildImporterConfiguration('software_package.csv', static::IMPORT_TYPE_SOFTWARE_PACKAGE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getSoftwareFeatureImporterConfiguration()
    {
        return $this->buildImporterConfiguration('software_feature.csv', static::IMPORT_TYPE_SOFTWARE_FEATURE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getVehicleTypeImporterConfiguration()
    {
        return $this->buildImporterConfiguration('vehicle_type.csv', static::IMPORT_TYPE_VEHICLE_TYPE);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getVehicleCategoryImporterConfiguration()
    {
        return $this->buildImporterConfiguration('vehicle_category.csv', static::IMPORT_TYPE_VEHICLE_CATEGORY);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getTourImporterConfiguration()
    {
        return $this
            ->buildImporterConfiguration('tour.csv', static::IMPORT_TYPE_TOUR);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDepositSkuImporterConfig()
    {
        return $this
            ->buildImporterConfiguration('deposit_sku.csv', static::IMPORT_TYPE_DEPOSIT_SKU);
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getLicenseKeyImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfigurationWithoutHeader(
                'license.csv',
                static::IMPORT_TYPE_LICENSE_KEY
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDrivingLicenceImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'driving-license.csv',
                self::IMPORT_TYPE_DRIVING_LICENCE
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getConcreteTourImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'concrete_tour.csv',
                self::IMPORT_TYPE_CONCRETE_TOUR
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getConcreteTimeSlotImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'concrete_time_slot.csv',
                self::IMPORT_TYPE_CONCRETE_TIME_SLOT
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDriverImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'driver.csv',
                self::IMPORT_TYPE_DRIVER
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getOrdersImporterConfiguration(): DataImporterConfigurationTransfer
    {
        $config = $this
            ->buildImporterConfiguration(
                'orders.csv',
                self::IMPORT_TYPE_ORDERS
            );
        $config
            ->getReaderConfiguration()
            ->setCsvEnclosure('\'');

        return $config;
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getDriverAppReleaseImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'driver_app_release.csv',
                self::IMPORT_TYPE_DRIVER_APP_RELEASE
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getBranchUserImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'branch_user.csv',
                self::IMPORT_TYPE_BRANCH_USER
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getGraphmastersSettingsImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'graphmasters_settings.csv',
                self::IMPORT_TYPE_GRAPHMASTERS_SETTINGS
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getGraphmastersDeliveryAreaCategoryImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'graphmasters_delivery_area_category.csv',
                self::IMPORT_TYPE_GRAPHMASTERS_DELIVERY_AREA_CATEGORY
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getGraphmastersOpeningTimesImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'graphmasters_opening_times.csv',
                self::IMPORT_TYPE_GRAPHMASTERS_OPENING_TIMES
            );
    }

    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getGraphmastersCommissioningTimesImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'graphmasters_commissioning_times.csv',
                self::IMPORT_TYPE_GRAPHMASTERS_COMMISSIONING_TIMES
            );
    }


    /**
     * @return DataImporterConfigurationTransfer
     */
    public function getMerchantUserImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this
            ->buildImporterConfiguration(
                'merchant_user.csv',
                self::IMPORT_TYPE_MERCHANT_USER
            );
    }

    /**
     * @param string $file
     * @param string $importType
     *
     * @return DataImporterConfigurationTransfer
     */
    protected function buildImporterConfiguration($file, $importType)
    {
        $dataImportReaderConfigurationTransfer = new DataImporterReaderConfigurationTransfer();
        $dataImportReaderConfigurationTransfer
            ->setFileName($this->getDataImportRootPath() . $file);

        $dataImporterConfigurationTransfer = new DataImporterConfigurationTransfer();
        $dataImporterConfigurationTransfer
            ->setImportType($importType)
            ->setReaderConfiguration($dataImportReaderConfigurationTransfer);

        return $dataImporterConfigurationTransfer;
    }

    /**
     * @param string $file
     * @param string $importType
     *
     * @return DataImporterConfigurationTransfer
     */
    protected function buildImporterConfigurationWithoutHeader(string $file, string $importType): DataImporterConfigurationTransfer
    {
        $dataImportReaderConfigurationTransfer = new DataImporterReaderConfigurationTransfer();
        $dataImportReaderConfigurationTransfer->setFileName($this->getDataImportRootPath() . $file);
        $dataImportReaderConfigurationTransfer->setCsvHasHeader(false);

        $dataImporterConfigurationTransfer = new DataImporterConfigurationTransfer();
        $dataImporterConfigurationTransfer
            ->setImportType($importType)
            ->setReaderConfiguration($dataImportReaderConfigurationTransfer);

        return $dataImporterConfigurationTransfer;
    }
}
