<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 11:46
 */

namespace Pyz\Client\AppRestApi\Zed;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class AppRestApiStub implements AppRestApiStubInterface
{
    const URL_GET_BRANCHES_BY_ZIP_CODE = '/merchant/gateway/get-branches-by-zip-code';
    const URL_GET_BRANCH_BY_ID = '/merchant/gateway/get-branch-by-id';
    const URL_GET_TIME_SLOTS_AND_TOTALS_FOR_BRANCHES_AND_PRODUCTS =
        '/app-rest-api/gateway/get-time-slots-and-totals-for-branches-and-products';
    const URL_GET_TIME_SLOTS_FOR_BRANCHES = '/delivery-area/gateway/get-time-slots-for-branches';
    const URL_GET_CATALOG_FOR_BRANCHES = '/merchant-price/gateway/get-catalog-for-branches';
    const URL_GET_BRANCH_BY_CODE = '/merchant/gateway/get-branch-by-code';
    const URL_GET_DELIVERY_AREAS_BY_ID_BRANCH = '/delivery-area/gateway/get-delivery-areas-by-id-branch';
    const URL_GET_CATEGORY_LIST = '/category/gateway/get-category-list';
    const URL_GET_PAYMENT_METHODS = '/merchant/gateway/get-payment-methods';
    const URL_GET_BRANCH_BY_ID_AND_ZIP_CODE = '/merchant/gateway/get-branch-by-id-and-zip-code';
    const URL_GET_WEIGHT_FOR_API_REQUEST = '/deposit/gateway/get-weight-for-api-request';
    const URL_GET_ACTIVE_DISCOUNTS_FOR_BRANCHES = '/discount/gateway/get-active-discounts-for-branches';
    const URL_GET_CATALOG_PRODUCT_FOR_BRANCH_BY_SKU = '/merchant-price/gateway/get-catalog-product-for-branch-by-sku';
    const URL_GET_ACTIVE_DISCOUNTS_FOR_PRODUCT = '/discount/gateway/get-active-discounts-for-product';
    const URL_CREATE_DEPOSIT_PICKUP_INQUIRY = '/deposit-pickup/gateway/save-inquiry';
    const URL_EVALUATE_TIME_SLOTS = '/graph-masters/gateway/evaluate-timeslots';

    //  Driver App
    protected const URL_DRIVER_APP_LOGIN = '/driver-app/gateway/login';
    protected const URL_DRIVER_APP_LOGOUT = '/driver-app/gateway/logout';
    protected const URL_DRIVER_APP_CLOSE_ORDER = '/driver-app/gateway/close-order';
    protected const URL_DRIVER_APP_GTIN = '/driver-app/gateway/gtin';
    protected const URL_DRIVER_APP_ORDER = '/driver-app/gateway/order';

    /**
     * @var ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * AppRestApiStub constructor.
     * @param ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }


    /**
     * @param AppApiRequestTransfer $transfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getBranchesByZipCode(AppApiRequestTransfer $transfer) : AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_BRANCHES_BY_ZIP_CODE,
            $transfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $transfer
     * @return BranchTransfer|TransferInterface
     */
    public function getBranchById(AppApiRequestTransfer $transfer) : BranchTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_BRANCH_BY_ID,
            $transfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $transfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getBranchByIdAndZipCode(AppApiRequestTransfer $transfer) : AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_BRANCH_BY_ID_AND_ZIP_CODE,
            $transfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $transfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getBranchByCode(AppApiRequestTransfer $transfer)
    {
        return $this->zedStub->call(
            self::URL_GET_BRANCH_BY_CODE,
            $transfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $transfer
     * @return mixed|TransferInterface
     */
    public function getDeliveryAreasByIdBranch(AppApiRequestTransfer $transfer)
    {
        return $this->zedStub->call(
            self::URL_GET_DELIVERY_AREAS_BY_ID_BRANCH,
            $transfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getPossibleTimeSlotsForBranches(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_TIME_SLOTS_FOR_BRANCHES,
            $requestTransfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getCatalogForBranches(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_CATALOG_FOR_BRANCHES,
            $requestTransfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getCategoryList(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_CATEGORY_LIST,
            $requestTransfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getPaymentMethods(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_PAYMENT_METHODS,
            $requestTransfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getWeightForApiRequest(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_WEIGHT_FOR_API_REQUEST,
            $requestTransfer,
            null
        );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getActiveDiscounts(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_GET_ACTIVE_DISCOUNTS_FOR_BRANCHES,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getDriverAppLogin(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_LOGIN,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getDriverAppLogout(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_LOGOUT,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getDriverAppCloseOrder(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_CLOSE_ORDER,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getDriverAppGtin(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_GTIN,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getDriverAppOrder(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_ORDER,
                $requestTransfer,
                null
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getCatalogProductForBranchBySku(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_GET_CATALOG_PRODUCT_FOR_BRANCH_BY_SKU,
                $requestTransfer
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getActiveDiscountsForProduct(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_GET_ACTIVE_DISCOUNTS_FOR_PRODUCT,
                $requestTransfer
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function createDepositPickupInquiry(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_CREATE_DEPOSIT_PICKUP_INQUIRY,
                $requestTransfer
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function evaluateTimeSlots(AppApiRequestTransfer  $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_EVALUATE_TIME_SLOTS,
                $requestTransfer
            );
    }
}
