<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 10:14
 */

namespace Pyz\Yves\AppRestApi\Plugin\Provider;

use Pyz\Yves\Application\Plugin\Provider\AbstractYvesControllerProvider;
use Pyz\Yves\AppRestApi\Controller\CityMerchantController;
use Pyz\Yves\AppRestApi\Controller\GraphmastersController;
use Pyz\Yves\AppRestApi\Controller\MerchantProductController;
use Pyz\Yves\AppRestApi\Controller\MerchantProductsController;
use Pyz\Yves\AppRestApi\Controller\MerchantTimeSlotController;
use Pyz\Yves\AppRestApi\Controller\OverviewController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AppRestApiControllerProvider extends AbstractYvesControllerProvider
{
    public const APP_REST_API_BRANCHES_BY_ZIP_CODE = 'app-rest-api-branches-by-zip-code';
    public const APP_REST_API_ORDER_PERSIST = 'app-rest-api-order-persist';
    public const APP_REST_API_TIME_SLOTS_AND_TOTALS = 'app-rest-api-time-slots-and-totals';
    public const APP_REST_API_BRANCH_BY_CODE = 'app-rest-api-branch-by-code';
    public const APP_REST_API_CITY_NAME_BY_ZIP_CODE = 'app-rest-api-city-name-by-zip-code';
    public const APP_REST_API_CATEGORY_LIST = 'app-rest-api-category-list';
    public const APP_REST_API_PAYMENT_AUTHORIZE = 'app-rest-api-payment-authorize';
    public const APP_REST_API_PAYMENT_STATUS = 'app-rest-api-payment-status';
    public const APP_REST_API_PAYMENT_STATUS_BY_ORDER_REF = 'app-rest-api-payment-status-by-order-ref';
    public const DRIVER_APP_REST_API_DRIVER_LOGIN = 'driver-app-rest-api-driver-login';
    public const DRIVER_APP_REST_API_DRIVER_LOGOUT = 'driver-app-rest-api-driver-logout';
    public const DRIVER_APP_REST_API_CLOSE_ORDER = 'driver-app-rest-api-close-order';
    public const DRIVER_APP_REST_API_DEPOSIT = 'driver-app-rest-api-deposit';
    public const DRIVER_APP_REST_API_GTIN = 'driver-app-rest-api-gtin';
    public const DRIVER_APP_REST_API_TOUR = 'driver-app-rest-api-tour';
    public const DRIVER_APP_REST_API_BRANCHES = 'driver-app-rest-api-branches';
    public const DRIVER_APP_LATEST_RELEASE = 'driver-app-latest-release';
    public const DRIVER_APP_DOWNLOAD_LATEST_RELEASE = 'driver-app-download-latest-release';
    public const APP_REST_API_CITY_MERCHANT_BY_ZIP_CODE_V1 = 'app-rest-api-city-merchant-by-zip-code-v1';
    public const APP_REST_API_CITY_MERCHANT_BY_ZIP_CODE_V2 = 'app-rest-api-city-merchant-by-zip-code-v2';
    public const APP_REST_API_MERCHANT_PRODUCT_BY_SKU = 'app-rest-api-merchant-product-by-sku';
    public const APP_REST_API_MERCHANT_PRODUCTS_BY_BRANCH_ID = 'app-rest-api-merchant-product-by-branch-id';
    public const APP_REST_API_MERCHANT_TIME_SLOT_V1 = 'app-rest-api-merchant-time-slot-v1';
    public const APP_REST_API_MERCHANT_TIME_SLOT_V2 = 'app-rest-api-merchant-time-slot-v2';
    public const APP_REST_API_OVERVIEW_CART_BY_TIME_SLOT_ID = 'app-rest-api-overview-cart-by-time-slot-id';
    public const APP_REST_API_OVERVIEW_CART_V2 = 'app-rest-api-overview-cart-v2';
    public const APP_REST_API_BRANCH_DELIVERS_ZIP_CODE = 'app-rest-api-branch-delivers-zip-code';
    public const APP_REST_API_DEPOSIT_PICKUP_CREATE_INQUIRY = 'app-rest-api-deposit-pickup-create-inquiry';
    public const DISCOUNT_API_GET_VALID_VOUCHER = 'discount-api-get-valid-voucher';
    public const DRIVER_APP_CANCEL_ORDER_BY_DRIVER = 'driver-app-cancel-order-by-driver';
    public const GRAPHMASTERS_API_EVALUATE_TIME_SLOTS = 'graphmasters-api-evaluate-timeslots';
    public const GRAPHMASTERS_API_GET_SETTINGS = 'graphmasters-api-get-settings';

        /**
         * @param Application $app
         *
         * @return void
         */
    protected function defineControllers(Application $app)
    {
        $this
            ->createController(
                '/api/branch',
                self::APP_REST_API_BRANCHES_BY_ZIP_CODE,
                'AppRestApi',
                'Branch',
                'getByZipCode'
            );

        $this
            ->createController(
                '/api/city',
                self::APP_REST_API_CITY_NAME_BY_ZIP_CODE,
                'AppRestApi',
                'DeliveryArea',
                'getCityNameByZipCode'
            );

        $this
            ->createController(
                '/api/order',
                self::APP_REST_API_ORDER_PERSIST,
                'AppRestApi',
                'Order',
                'persist'
            );

        $this
            ->createController(
                '/api/voucher',
                self::APP_REST_API_BRANCH_BY_CODE,
                'AppRestApi',
                'Voucher',
                'getByCode'
            );

        $this
            ->createController(
                '/api/time-slots',
                self::APP_REST_API_TIME_SLOTS_AND_TOTALS,
                'AppRestApi',
                'TimeSlot',
                'getForBranches'
            );

        $this
            ->createController(
                '/api/category-list',
                self::APP_REST_API_CATEGORY_LIST,
                'AppRestApi',
                'Category',
                'getCategoryList'
            );

        $this
            ->createController(
                '/api/payment/authorize',
                self::APP_REST_API_PAYMENT_AUTHORIZE,
                'AppRestApi',
                'Payment',
                'authorize'
            );

        $this
            ->createController(
                '/api/payment/status-by-order-ref',
                self::APP_REST_API_PAYMENT_STATUS_BY_ORDER_REF,
                'AppRestApi',
                'Payment',
                'statusByOrderRef'
            );

        $this
            ->createController(
                '/api/driver-login',
                self::DRIVER_APP_REST_API_DRIVER_LOGIN,
                'AppRestApi',
                'DriverApp',
                'login'
            );

        $this
            ->createController(
                '/api/driver-logout',
                self::DRIVER_APP_REST_API_DRIVER_LOGOUT,
                'AppRestApi',
                'DriverApp',
                'logout'
            );

        $this
            ->createController(
                '/api/driver-close-order',
                self::DRIVER_APP_REST_API_CLOSE_ORDER,
                'AppRestApi',
                'DriverApp',
                'closeOrder'
            );

        $this
            ->createController(
                '/api/driver-deposit',
                self::DRIVER_APP_REST_API_DEPOSIT,
                'AppRestApi',
                'DriverApp',
                'deposit'
            );

        $this
            ->createController(
                '/api/driver-gtin',
                self::DRIVER_APP_REST_API_GTIN,
                'AppRestApi',
                'DriverApp',
                'gtin'
            );

        $this
            ->createController(
                '/api/driver-tour',
                self::DRIVER_APP_REST_API_TOUR,
                'AppRestApi',
                'DriverApp',
                'tour'
            );

        $this
            ->createController(
                '/api/driver-branches',
                self::DRIVER_APP_REST_API_BRANCHES,
                'AppRestApi',
                'DriverApp',
                'branches'
            );

        $this
            ->createController(
                '/api/driver-cancel-order',
                self::DRIVER_APP_CANCEL_ORDER_BY_DRIVER,
                'AppRestApi',
                'DriverApp',
                'cancelOrder'
            );

        $this
            ->createController(
                '/api/driver-latest-release',
                self::DRIVER_APP_LATEST_RELEASE,
                'AppRestApi',
                'DriverApp',
                'latestRelease'
            );

        $this
            ->createController(
                '/api/driver-download-latest-release',
                self::DRIVER_APP_DOWNLOAD_LATEST_RELEASE,
                'AppRestApi',
                'DriverApp',
                'downloadLatestRelease'
            );

        $this->defineCityMerchantControllers();

        $this
            ->createGetController(
                sprintf(
                    '/api/merchant/{%s}/products/{%s}',
                    MerchantProductsController::KEY_GET_BRANCH_ID,
                    MerchantProductsController::KEY_GET_VERSION
                ),
                self::APP_REST_API_MERCHANT_PRODUCTS_BY_BRANCH_ID,
                'AppRestApi',
                'MerchantProducts',
                'getProductsByBranchId'
            )
            ->assert(
                MerchantProductsController::KEY_GET_BRANCH_ID,
                '[0-9]+'
            )
            ->assert(
                MerchantProductsController::KEY_GET_VERSION,
                '^(v[0-9]+)?'
            );

        $this
            ->createPostController(
                sprintf(
                    '/api/merchant/{%s}/product',
                    MerchantProductController::KEY_GET_BRANCH_ID
                ),
                self::APP_REST_API_MERCHANT_PRODUCT_BY_SKU,
                'AppRestApi',
                'MerchantProduct',
                'getProductBySku'
            )
            ->assert(
                MerchantProductController::KEY_GET_BRANCH_ID,
                '[0-9]+'
            );

        $this
            ->createController(
                '/api/merchant/time-slots',
                self::APP_REST_API_MERCHANT_TIME_SLOT_V1,
                'AppRestApi',
                'MerchantTimeSlot',
                'getForMerchant'
            );

        $this
            ->createController(
                sprintf(
                    '/api/merchant/time-slots/{%s}',
                    MerchantTimeSlotController::KEY_GET_VERSION
                ),
                self::APP_REST_API_MERCHANT_TIME_SLOT_V2,
                'AppRestApi',
                'MerchantTimeSlot',
                'getForMerchant'
            )
            ->assert(
                MerchantTimeSlotController::KEY_GET_VERSION,
                '^(v[0-9]+)?'
            );

        $this
            ->createController(
                '/api/overview',
                self::APP_REST_API_OVERVIEW_CART_BY_TIME_SLOT_ID,
                'AppRestApi',
                'Overview',
                'getPriceAndExpenseOverview'
            );

        $this
            ->createController(
                sprintf(
                    '/api/overview/{%s}',
                    OverviewController::KEY_GET_VERSION
                ),
                self::APP_REST_API_OVERVIEW_CART_V2,
                'AppRestApi',
                'Overview',
                'getPriceAndExpenseOverview'
            )
            ->assert(
                OverviewController::KEY_GET_VERSION,
                '^(v[0-9]+)?'
            );

        $this
            ->createController(
                '/api/delivery-area/branch-delivers',
                self::APP_REST_API_BRANCH_DELIVERS_ZIP_CODE,
                'AppRestApi',
                'DeliveryArea',
                'getBranchDelivers'
            );

        $this
            ->createController(
                '/api/discount/check-valid-voucher',
                self::DISCOUNT_API_GET_VALID_VOUCHER,
                'AppRestApi',
                'Discount',
                'checkValidVoucher'
            );

        /*
        $this
            ->createPostController(
                '/api/deposit-pickup/inquiry',
                self::APP_REST_API_DEPOSIT_PICKUP_CREATE_INQUIRY,
                'AppRestApi',
                'DepositPickup',
                'createInquiry'
            );
        */

        $this
            ->createPostController(
                '/api/timeslots/evaluate',
                self::GRAPHMASTERS_API_EVALUATE_TIME_SLOTS,
                'AppRestApi',
                'Graphmasters',
                'evaluateTimeSlots'
            );

        $this
            ->createGetController(
                sprintf(
                    '/api/tour-settings/{%s}',
                    GraphmastersController::KEY_GET_BRANCH_ID
                ),
                self::GRAPHMASTERS_API_GET_SETTINGS,
                'AppRestApi',
                'Graphmasters',
                'getSettings'
            )->assert(
                GraphmastersController::KEY_GET_BRANCH_ID,
                '[0-9]+'
            );
    }

    /**
     * @param mixed $unusedParameter
     * @param Request $request
     * @return string
     */
    public function getBranchCodeFromRequest(
        $unusedParameter,
        Request $request
    ): string
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            return (string)$request
                ->request
                ->get(
                    CityMerchantController::KEY_BRANCH_CODE,
                    ''
                );
        }

        return (string)$request
            ->query
            ->get(
                CityMerchantController::KEY_BRANCH_CODE,
                ''
            );
    }

    protected function defineCityMerchantControllers(): void
    {
        // v1
        $this
            ->createGetController(
                sprintf(
                    '/api/city/{%s}/merchants',
                    CityMerchantController::KEY_GET_ZIP_CODE
                ),
                self::APP_REST_API_CITY_MERCHANT_BY_ZIP_CODE_V1,
                'AppRestApi',
                'CityMerchant',
                'getMerchantsByZipCode'
            )
            ->assert(
                CityMerchantController::KEY_GET_ZIP_CODE,
                '[0-9]{0,5}'
            )
            ->value(
                CityMerchantController::KEY_GET_ZIP_CODE,
                ''
            )
            ->convert(
                CityMerchantController::KEY_BRANCH_CODE,
                [
                    $this,
                    'getBranchCodeFromRequest'
                ]
            );

        // v2
        $this
            ->createPostController(
                sprintf(
                    '/api/city/merchants/{%s}',
                    CityMerchantController::KEY_VERSION
                ),
                self::APP_REST_API_CITY_MERCHANT_BY_ZIP_CODE_V2,
                'AppRestApi',
                'CityMerchant',
                'getMerchantsByZipCode'
            )
            ->assert(
                CityMerchantController::KEY_VERSION,
                '^(v[0-9]+)?'
            );
    }
}
