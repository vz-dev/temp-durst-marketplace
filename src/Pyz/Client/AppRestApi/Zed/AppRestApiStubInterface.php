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
use Spryker\Shared\Kernel\Transfer\TransferInterface;

interface AppRestApiStubInterface
{
    /**
     * @param AppApiRequestTransfer $transfer
     * @return AppApiResponseTransfer
     */
    public function getBranchesByZipCode(AppApiRequestTransfer $transfer) : AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $transfer
     * @return BranchTransfer
     */
    public function getBranchById(AppApiRequestTransfer $transfer) : BranchTransfer;

    /**
     * @param AppApiRequestTransfer $transfer
     * @return mixed
     */
    public function getBranchByCode(AppApiRequestTransfer $transfer);

    /**
     * @param AppApiRequestTransfer $transfer
     * @return mixed
     */
    public function getDeliveryAreasByIdBranch(AppApiRequestTransfer $transfer);

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getDriverAppOrder(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getPossibleTimeSlotsForBranches(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCatalogForBranches(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCategoryList(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getPaymentMethods(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getWeightForApiRequest(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getActiveDiscounts(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getCatalogProductForBranchBySku(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getActiveDiscountsForProduct(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function evaluateTimeSlots(AppApiRequestTransfer  $requestTransfer): AppApiResponseTransfer;
}
