<?php
/**
 * Durst - project - AppRestApiConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.18
 * Time: 17:25
 */

namespace Pyz\Shared\AppRestApi;


interface AppRestApiConstants
{
    public const SCHEMA_FOLDER_PATH = 'SCHEMA_FILE_PATH';
    public const SCHEMA_TIME_SLOT_REQUEST = 'SCHEMA_TIME_SLOT_REQUEST';
    public const SCHEMA_TIME_SLOT_RESPONSE = 'SCHEMA_TIME_SLOT_RESPONSE';
    public const SCHEMA_BRANCH_REQUEST = 'SCHEMA_BRANCH_REQUEST';
    public const SCHEMA_BRANCH_RESPONSE = 'SCHEMA_BRANCH_RESPONSE';
    public const SCHEMA_CITY_REQUEST = 'SCHEMA_CITY_REQUEST';
    public const SCHEMA_CITY_RESPONSE = 'SCHEMA_CITY_RESPONSE';
    public const SCHEMA_ORDER_REQUEST = 'SCHEMA_ORDER_REQUEST';
    public const SCHEMA_ORDER_RESPONSE = 'SCHEMA_ORDER_RESPONSE';
    public const SCHEMA_PAYMENT_STATUS_REQUEST = 'SCHEMA_PAYMENT_STATUS_REQUEST';
    public const SCHEMA_PAYMENT_STATUS_RESPONSE = 'SCHEMA_PAYMENT_STATUS_RESPONSE';
    public const SCHEMA_VOUCHER_REQUEST = 'SCHEMA_VOUCHER_REQUEST';
    public const SCHEMA_VOUCHER_RESPONSE = 'SCHEMA_VOUCHER_RESPONSE';
    public const SCHEMA_CATEGORY_RESPONSE = 'SCHEMA_CATEGORY_RESPONSE';
    public const SCHEMA_DRIVER_APP_LOGIN_REQUEST = 'SCHEMA_DRIVER_APP_LOGIN_REQUEST';
    public const SCHEMA_DRIVER_APP_LOGIN_RESPONSE = 'SCHEMA_DRIVER_APP_LOGIN_RESPONSE';
    public const SCHEMA_DRIVER_APP_LOGOUT_REQUEST = 'SCHEMA_DRIVER_APP_LOGOUT_REQUEST';
    public const SCHEMA_DRIVER_APP_LOGOUT_RESPONSE = 'SCHEMA_DRIVER_APP_LOGOUT_RESPONSE';
    public const SCHEMA_DRIVER_APP_CLOSE_ORDER_REQUEST = 'SCHEMA_DRIVER_APP_CLOSE_ORDER_REQUEST';
    public const SCHEMA_DRIVER_APP_CLOSE_ORDER_RESPONSE = 'SCHEMA_DRIVER_APP_CLOSE_ORDER_RESPONSE';
    public const SCHEMA_DRIVER_APP_DEPOSIT_REQUEST = 'SCHEMA_DRIVER_APP_DEPOSIT_REQUEST';
    public const SCHEMA_DRIVER_APP_DEPOSIT_RESPONSE = 'SCHEMA_DRIVER_APP_DEPOSIT_RESPONSE';
    public const SCHEMA_DRIVER_APP_GTIN_REQUEST = 'SCHEMA_DRIVER_APP_GTIN_REQUEST';
    public const SCHEMA_DRIVER_APP_GTIN_RESPONSE = 'SCHEMA_DRIVER_APP_GTIN_RESPONSE';
    public const SCHEMA_DRIVER_APP_TOUR_REQUEST = 'SCHEMA_DRIVER_APP_TOUR_REQUEST';
    public const SCHEMA_DRIVER_APP_TOUR_RESPONSE = 'SCHEMA_DRIVER_APP_TOUR_RESPONSE';
    public const SCHEMA_DRIVER_APP_ORDER_REQUEST = 'SCHEMA_DRIVER_APP_ORDER_REQUEST';
    public const SCHEMA_DRIVER_APP_ORDER_RESPONSE = 'SCHEMA_DRIVER_APP_ORDER_RESPONSE';
    public const SCHEMA_DRIVER_APP_BRANCHES_REQUEST = 'SCHEMA_DRIVER_APP_BRANCHES_REQUEST';
    public const SCHEMA_DRIVER_APP_BRANCHES_RESPONSE = 'SCHEMA_DRIVER_APP_BRANCHES_RESPONSE';
    public const SCHEMA_DRIVER_APP_LATEST_RELEASE_REQUEST = 'SCHEMA_DRIVER_APP_BRANCHES_REQUEST';
    public const SCHEMA_DRIVER_APP_LATEST_RELEASE_RESPONSE = 'SCHEMA_DRIVER_APP_BRANCHES_RESPONSE';
    public const SCHEMA_DRIVER_APP_DOWNLOAD_LATEST_RELEASE_REQUEST = 'SCHEMA_DRIVER_APP_DOWNLOAD_LATEST_RELEASE_REQUEST';
    public const SCHEMA_CITY_MERCHANT_REQUEST_V1 = 'SCHEMA_CITY_MERCHANT_REQUEST_V1';
    public const SCHEMA_CITY_MERCHANT_REQUEST_V2 = 'SCHEMA_CITY_MERCHANT_REQUEST_V2';
    public const SCHEMA_CITY_MERCHANT_RESPONSE = 'SCHEMA_CITY_MERCHANT_RESPONSE';
    public const SCHEMA_MERCHANT_PRODUCT_REQUEST = 'SCHEMA_MERCHANT_PRODUCT_REQUEST';
    public const SCHEMA_MERCHANT_PRODUCT_RESPONSE = 'SCHEMA_MERCHANT_PRODUCT_RESPONSE';
    public const SCHEMA_MERCHANT_PRODUCTS_REQUEST_V1 = 'SCHEMA_MERCHANT_PRODUCTS_REQUEST_V1';
    public const SCHEMA_MERCHANT_PRODUCTS_REQUEST_V2 = 'SCHEMA_MERCHANT_PRODUCTS_REQUEST_V2';
    public const SCHEMA_MERCHANT_PRODUCTS_REQUEST_V3 = 'SCHEMA_MERCHANT_PRODUCTS_REQUEST_V3';
    public const SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V1 = 'SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V1';
    public const SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V2 = 'SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V2';
    public const SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V3 = 'SCHEMA_MERCHANT_PRODUCTS_RESPONSE_V3';
    public const SCHEMA_MERCHANT_TIME_SLOT_REQUEST = 'SCHEMA_MERCHANT_TIME_SLOT_REQUEST';
    public const SCHEMA_MERCHANT_TIME_SLOT_RESPONSE = 'SCHEMA_MERCHANT_TIME_SLOT_RESPONSE';
    public const SCHEMA_EVALUATE_TIME_SLOT_REQUEST = 'SCHEMA_EVALUATE_TIME_SLOT_REQUEST';
    public const SCHEMA_EVALUATE_TIME_SLOT_RESPONSE = 'SCHEMA_EVALUATE_TIME_SLOT_RESPONSE';
    public const SCHEMA_OVERVIEW_REQUEST = 'SCHEMA_OVERVIEW_REQUEST';
    public const SCHEMA_OVERVIEW_RESPONSE = 'SCHEMA_OVERVIEW_RESPONSE';
    public const SCHEMA_DISCOUNT_REQUEST = 'SCHEMA_DISCOUNT_REQUEST';
    public const SCHEMA_DISCOUNT_RESPONSE = 'SCHEMA_DISCOUNT_RESPONSE';
    public const SCHEMA_DELIVERY_AREA_REQUEST = 'SCHEMA_DELIVERY_AREA_REQUEST';
    public const SCHEMA_DELIVERY_AREA_RESPONSE = 'SCHEMA_DELIVERY_AREA_RESPONSE';
    public const SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_REQUEST = 'SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_REQUEST';
    public const SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_RESPONSE = 'SCHEMA_DEPOSIT_PICKUP_CREATE_INQUIRY_RESPONSE';
    public const SCHEMA_DRIVER_APP_CANCEL_ORDER_REQUEST = 'SCHEMA_DRIVER_APP_CANCEL_ORDER_REQUEST';
    public const SCHEMA_DRIVER_APP_CANCEL_ORDER_RESPONSE = 'SCHEMA_DRIVER_APP_CANCEL_ORDER_RESPONSE';

    public const MEDIA_SERVER_HOST = 'MEDIA_SERVER_HOST';
    public const FALLBACK_IMAGE_PRODUCT = 'FALLBACK_IMAGE_PRODUCT';

    public const IMAGE_SCALING_PATH = 'IMAGE_SCALING_PATH';
    public const IMAGE_SCALING_PATH_THUMB = 'IMAGE_SCALING_PATH_THUMB';
    public const IMAGE_SCALING_PATH_BIG = 'IMAGE_SCALING_PATH_BIG';

    public const UPLOAD_BRANCH_FOLDER_HOST = 'UPLOAD_BRANCH_FOLDER_HOST';
    public const UPLOAD_BRANCH_FOLDER_DIR = 'UPLOAD_BRANCH_FOLDER_DIR';

    public const UPLOAD_PAYMENT_METHOD_DIR = 'UPLOAD_PAYMENT_METHOD_DIR';

    public const ANALYTICS_BRANCH_LOG_FILE_PATH_YVES = 'LOG:ANALYTICS_BRANCH_LOG_FILE_PATH_YVES';
    public const ANALYTICS_TIME_SLOT_LOG_FILE_PATH_YVES = 'LOG:ANALYTICS_TIME_SLOT_LOG_FILE_PATH_YVES';
    public const ANALYTICS_MERCHANT_TIME_SLOT_LOG_FILE_PATH_YVES = 'LOG:ANALYTICS_MERCHANT_TIME_SLOT_LOG_FILE_PATH_YVES';
    public const ANALYTICS_OVERVIEW_LOG_FILE_PATH_YVES = 'LOG:ANALYTICS_OVERVIEW_LOG_FILE_PATH_YVES';

    public const API_TIME_SLOTS_MAX = 'API_TIME_SLOTS_MAX';
    public const API_TIME_SLOTS_ITEMS_PER_SLOT = 'API_TIME_SLOTS_ITEMS_PER_SLOT';
    public const API_TIME_SLOTS_DAY_LIMIT = 'API_TIME_SLOTS_DAY_LIMIT';
    public const API_GM_TIME_SLOTS_DAY_LIMIT = 'API_GM_TIME_SLOTS_DAY_LIMIT';
}
