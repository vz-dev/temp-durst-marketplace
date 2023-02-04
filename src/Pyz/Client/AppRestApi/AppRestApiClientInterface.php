<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 11:23
 */

namespace Pyz\Client\AppRestApi;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Shared\ZedRequest\Client\Exception\RequestException;

interface AppRestApiClientInterface
{
    /**
     * Receives all branches that have at least one time slot defined in the delivery area
     * that matches the zip code given by the request transfer and returns a response
     * transfer containing them.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getBranchesByZipCode(AppApiRequestTransfer $requestTransfer) : AppApiResponseTransfer;


    /**
     * retrieves the branch matching the given id and returns a responsetransfer with the corresponding
     * branch
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return BranchTransfer
     */
    public function getBranchById(AppApiRequestTransfer $requestTransfer) : BranchTransfer;

    /**
     * Returns a response transfer object containing a
     * fully hydrated branch transfer object representing the branch with the given id. If the branch
     * does not deliver to the delivery area with the given zip code or no branch with the given id
     * exists the branch property of the response object will contain null.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getBranchByIdAndZipCode(AppApiRequestTransfer $requestTransfer) : AppApiResponseTransfer;

    /**
     * Computes the earliest possible time slots for the given branch ids and calculates total costs for the given
     * product for all given branches. Optionally also fetches fully booked time slots.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @param bool $fetchFullyBookedTimeSlots
     * @return AppApiResponseTransfer
     */
    public function getPossibleTimeSlotsForBranches(
        AppApiRequestTransfer $requestTransfer,
        bool $fetchFullyBookedTimeSlots = false
    ): AppApiResponseTransfer;


    /**
     * Receives the branch with a specific code (given by the request transfer object)
     * from zed and returns it in the response transfer.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getBranchByCode(AppApiRequestTransfer $requestTransfer);


    /**
     * Receives all Delivery areas based on Id Branch (given by the request transfer object)
     * from zed and returns it in the response transfer.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     * @throws RequestException if the return code of the Zed-Request != 200
     */
    public function getDeliveryAreasByIdBranch(AppApiRequestTransfer $requestTransfer);

    /**
     * Receives all categories, products, units and prices for the given branches.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCatalogForBranches(AppApiRequestTransfer $requestTransfer);

    /**
     * Gets all active categories with attributes.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return mixed
     */
    public function getCategoryList(AppApiRequestTransfer $requestTransfer);

    /**
     * Returns an array of all payment methods that are supported by the branches with
     * the given ids.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return mixed
     */
    public function getPaymentMethods(AppApiRequestTransfer $requestTransfer);

    /**
     * Returns the weight of all the items in the passed apirequesttransfer
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getWeight(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * Returns all discounts for an item from the passed api request transfer
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getDiscounts(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param int[] $idTimeSlots
     * @return ConcreteTimeSlotTransfer[]
     */
    public function getTimeSlotsByIds(array $idTimeSlots): array;

    /**
     * Retrieves the product and its units and prices identified by the branch ID and SKU in the given transfer.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCatalogProductForBranchBySku(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * Retrieves all discounts for the product identified by the branch ID and SKU in the given transfer.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getDiscountsForProduct(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * Creates a deposit pickup inquiry and persists it to the database
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function createDepositPickupInquiry(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function evaluateTimeSlots(AppApiRequestTransfer  $requestTransfer): AppApiResponseTransfer;

    /**
     * Evaluate GM Timeslots
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiRequestTransfer
     */
    public function evaluateGMTimeSlots(AppApiRequestTransfer $requestTransfer): AppApiRequestTransfer;

    /**
     * get GM settings, including settings, opening-times, commissionings times etc, for a branch with the given id
     *
     * @param int $idBranch
     * @return array
     */
    public function getGMSettings(int $idBranch): array;
}
