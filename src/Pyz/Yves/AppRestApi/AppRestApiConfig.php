<?php
/**
 * Durst - project - AppRestApiConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 09:26
 */

namespace Pyz\Yves\AppRestApi;

use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Pyz\Shared\DriverApp\DriverAppConfig;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

class AppRestApiConfig extends AbstractBundleConfig
{
    public const FILE_PREFIX = 'file://';

    public const DEFAULT_SOFTWARE_FEATURE_ALLOW_COMMENTS = 'allow_order_comments';

    /**
     * @return string
     */
    public function getDriverAppApkUploadPath(): string
    {
        return $this
            ->get(
                DriverAppConfig::DRIVER_APP_UPLOAD_PATH
            );
    }

    /**
     * @return string
     */
    public function getTimeSlotRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_TIME_SLOT_REQUEST));
    }

    /**
     * @return string
     */
    public function getTimeSlotResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_TIME_SLOT_RESPONSE));
    }

    /**
     * @return string
     */
    public function getBranchRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_BRANCH_REQUEST));
    }

    /**
     * @return string
     */
    public function getBranchResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_BRANCH_RESPONSE));
    }

    /**
     * @return string
     */
    public function getOrderRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_ORDER_REQUEST));
    }

    /**
     * @return string
     */
    public function getOrderResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_ORDER_RESPONSE));
    }

    /**
     * @return string
     */
    public function getPaymentStatusRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_PAYMENT_STATUS_REQUEST));
    }

    /**
     * @return string
     */
    public function getPaymentStatusResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_PAYMENT_STATUS_RESPONSE));
    }

    /**
     * @return string
     */
    public function getVoucherRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_VOUCHER_REQUEST));
    }

    /**
     * @return string
     */
    public function getVoucherResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_VOUCHER_RESPONSE));
    }

    /**
     * @return string
     */
    public function getCategoryResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath($this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_CATEGORY_RESPONSE));
    }

    /**
     * @return string
     */
    protected function getSchemaFolderPath(): string
    {
        return $this
            ->get(AppRestApiConstants::SCHEMA_FOLDER_PATH);
    }

    /**
     * @return string
     */
    public function getMediaServerHost(): string
    {
        return $this
            ->get(AppRestApiConstants::MEDIA_SERVER_HOST);
    }

    /**
     * @return string
     */
    public function getFallbackImageProduct(): string
    {
        return $this
            ->get(AppRestApiConstants::FALLBACK_IMAGE_PRODUCT);
    }

    /**
     * @return string
     */
    public function getThumbImageHost(): string
    {
        return sprintf(
            '%s%s',
            $this
                ->getMediaServerHost(),
            $this
                ->get(AppRestApiConstants::IMAGE_SCALING_PATH_THUMB)
        );
    }

    /**
     * @return string
     */
    public function getBigImageHost(): string
    {
        return sprintf(
            '%s%s',
            $this
                ->getMediaServerHost(),
            $this
                ->get(AppRestApiConstants::IMAGE_SCALING_PATH_BIG)
        );
    }

    /**
     * @return string
     */
    public function getMerchantUploadPath(): string
    {
        return sprintf(
            '%s%s',
            $this
                ->get(AppRestApiConstants::UPLOAD_BRANCH_FOLDER_HOST),
            $this
                ->get(AppRestApiConstants::UPLOAD_BRANCH_FOLDER_DIR)
        );
    }

    /**
     * @return string
     */
    public function getAnalyticsBranchLogFilePath(): string
    {
        return $this
            ->get(AppRestApiConstants::ANALYTICS_BRANCH_LOG_FILE_PATH_YVES);
    }

    /**
     * @return string
     */
    public function getAnalyticsTimeSlotLogFilePath(): string
    {
        return $this
            ->get(AppRestApiConstants::ANALYTICS_TIME_SLOT_LOG_FILE_PATH_YVES);
    }

    /**
     * @return int
     */
    public function getTimeSlotMaxSlots(): int
    {
        return $this
            ->get(AppRestApiConstants::API_TIME_SLOTS_MAX);
    }

    /**
     * @return int
     */
    public function getTimeSlotMaxPerItem(): int
    {
        return $this
            ->get(AppRestApiConstants::API_TIME_SLOTS_ITEMS_PER_SLOT);
    }

    /**
     * @return string
     */
    public function getAllowCommentsSoftwareFeatureCode(): string
    {
        return $this
            ->get(SoftwarePackageConstants::SOFTWARE_FEATURE_ALLOW_COMMENTS, static::DEFAULT_SOFTWARE_FEATURE_ALLOW_COMMENTS);
    }

    /**
     * @return string
     */
    public function getSepaMandateUrl(): string
    {
        return $this
            ->get(
                HeidelpayRestConstants::HEIDELPAY_REST_SEPA_MANDATE_URL
            );
    }

    /**
     * @return string
     */
    public function getDriverLoginRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LOGIN_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverLoginResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LOGIN_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverLogoutRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LOGOUT_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverLogoutResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LOGOUT_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverCloseOrderRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_CLOSE_ORDER_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverCloseOrderResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                ->getSchemaFolderPath() . $this
                ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_CLOSE_ORDER_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverDepositRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_DEPOSIT_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverDepositResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_DEPOSIT_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverGtinRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_GTIN_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverGtinResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_GTIN_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverCancelOrderRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() .
                $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_CANCEL_ORDER_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverCancelOrderResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() .
                $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_CANCEL_ORDER_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getCityRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_CITY_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getCityResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_CITY_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverTourRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_TOUR_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverTourResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_TOUR_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverOrderRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_ORDER_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverOrderResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_ORDER_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverBranchRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_BRANCHES_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverBranchResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_BRANCHES_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDriverLatestReleaseRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LATEST_RELEASE_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverDownloadLatestReleaseRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_DOWNLOAD_LATEST_RELEASE_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDriverLatestReleaseResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DRIVER_APP_LATEST_RELEASE_RESPONSE)
            );
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public function getCityMerchantsRequestSchemaPath(string $version): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(constant(AppRestApiConstants::class . '::SCHEMA_CITY_MERCHANT_REQUEST_' . ucfirst($version)))
            );
    }

    /**
     * @return string
     */
    public function getCityMerchantsResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_CITY_MERCHANT_RESPONSE)
            );
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public function getMerchantProductsRequestSchemaPath(string $version): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(constant(AppRestApiConstants::class . '::SCHEMA_MERCHANT_PRODUCTS_REQUEST_' . ucfirst($version)))
            );
    }

    /**
     * @return string
     */
    public function getMerchantProductRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_MERCHANT_PRODUCT_REQUEST)
            );
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public function getMerchantProductsResponseSchemaPath(string $version): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(constant(AppRestApiConstants::class . '::SCHEMA_MERCHANT_PRODUCTS_RESPONSE_' . ucfirst($version)))
            );
    }

    /**
     * @return string
     */
    public function getMerchantProductResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_MERCHANT_PRODUCT_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getPaymentUploadPath(): string
    {
        return sprintf(
            '%s%s',
            $this
                ->get(AppRestApiConstants::MEDIA_SERVER_HOST),
            $this
                ->get(AppRestApiConstants::UPLOAD_PAYMENT_METHOD_DIR)
        );
    }

    /**
     * @return string
     */
    public function getMerchantTimeSlotsRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_MERCHANT_TIME_SLOT_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getMerchantTimeSlotsResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_MERCHANT_TIME_SLOT_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getAnalyticsMerchantTimeSlotLogFilePath(): string
    {
        return $this
            ->get(AppRestApiConstants::ANALYTICS_MERCHANT_TIME_SLOT_LOG_FILE_PATH_YVES);
    }

    /**
     * @return string
     */
    public function getOverviewRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_OVERVIEW_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getOverviewResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_OVERVIEW_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDiscountRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DISCOUNT_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDiscountResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DISCOUNT_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getDeliveryAreaRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DELIVERY_AREA_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDeliveryAreaResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_DELIVERY_AREA_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getAnalyticsOverviewLogFilePath(): string
    {
        return $this
            ->get(AppRestApiConstants::ANALYTICS_OVERVIEW_LOG_FILE_PATH_YVES);
    }

    /**
     * @return string
     */
    public function getDurstHeidelpayPublicKey(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getHeidelPayStartDateBranchSpecificKeys(): string
    {
        return $this
            ->get(HeidelpayRestConstants::HEIDELPAY_REST_START_DATE_BRANCH_SPECIFIC_KEYS);
    }

    /**
     * @return string
     */
    public function getOmsStateReadyForDelivery() : string
    {
        return $this
            ->get(OmsConstants::OMS_WHOLESALE_ACCEPTED_STATE);
    }

    /**
     * @return string
     */
    public function getDepositPickupCreateInquiryRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this->getSchemaFolderPath() .
                $this->get(AppRestApiConstants::SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getDepositPickupCreateInquiryResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this->getSchemaFolderPath() .
                $this->get(AppRestApiConstants::SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_RESPONSE)
            );
    }

    /**
     * @return string
     */
    public function getEvaluateTimeSlotsRequestSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_EVALUATE_TIME_SLOT_REQUEST)
            );
    }

    /**
     * @return string
     */
    public function getEvaluateTimeSlotsResponseSchemaPath(): string
    {
        return self::FILE_PREFIX .
            realpath(
                $this
                    ->getSchemaFolderPath() . $this
                    ->get(AppRestApiConstants::SCHEMA_EVALUATE_TIME_SLOT_RESPONSE)
            );
    }
}
