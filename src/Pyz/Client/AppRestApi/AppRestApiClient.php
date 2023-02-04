<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 10:00
 */

namespace Pyz\Client\AppRestApi;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Spryker\Client\Kernel\AbstractClient;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

/**
 * Class AppRestApiClient
 * @package Pyz\Client\AppRestApi
 * @method AppRestApiFactory getFactory()
 */
class AppRestApiClient extends AbstractClient implements AppRestApiClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getBranchesByZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getBranchesByZipCode($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return BranchTransfer
     */
    public function getBranchById(AppApiRequestTransfer $requestTransfer): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getBranchById($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getBranchByCode(AppApiRequestTransfer $requestTransfer)
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getBranchByCode($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return TransferInterface
     */
    public function getDeliveryAreasByIdBranch(AppApiRequestTransfer $requestTransfer)
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getDeliveryAreasByIdBranch($requestTransfer);
    }

    /**
     * Computes the earliest possible time slots for the given branch ids and calculates total costs for the given
     * product for all given branches.
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @param bool $fetchFullyBookedTimeSlots
     *
     * @return AppApiResponseTransfer
     */
    public function getPossibleTimeSlotsForBranches(
        AppApiRequestTransfer $requestTransfer,
        bool $fetchFullyBookedTimeSlots = false
    ): AppApiResponseTransfer {
        return $this
            ->getFactory()
            ->createSearchStub()
            ->getTimeSlotsForBranches($requestTransfer, $fetchFullyBookedTimeSlots);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function getCatalogForBranches(AppApiRequestTransfer $requestTransfer)
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getCatalogForBranches($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|mixed|TransferInterface
     */
    public function getCategoryList(AppApiRequestTransfer $requestTransfer)
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getCategoryList($requestTransfer);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     *
     * @return AppApiResponseTransfer|mixed|TransferInterface
     */
    public function getPaymentMethods(AppApiRequestTransfer $requestTransfer)
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getPaymentMethods($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     *
     * @return AppApiResponseTransfer
     */
    public function getBranchByIdAndZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getBranchByIdAndZipCode($requestTransfer);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getWeight(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getWeightForApiRequest($requestTransfer);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getDiscounts(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getActiveDiscounts($requestTransfer);
    }

    /**
     * {@inheritDoc}
     * @param int[] $idTimeSlots
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer[]
     */
    public function getTimeSlotsByIds(array $idTimeSlots): array
    {
        return $this
            ->getFactory()
            ->createSearchStub()
            ->getTimeSlotsForIds($idTimeSlots);
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogProductForBranchBySku(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getCatalogProductForBranchBySku($requestTransfer);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountsForProduct(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->getActiveDiscountsForProduct($requestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer|TransferInterface
     */
    public function createDepositPickupInquiry(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->createDepositPickupInquiry($requestTransfer);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function evaluateTimeSlots(AppApiRequestTransfer  $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAppRestApiStub()
            ->evaluateTimeSlots($requestTransfer);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiRequestTransfer
     */
    public function evaluateGMTimeSlots(AppApiRequestTransfer  $requestTransfer): AppApiRequestTransfer
    {
        return $this
            ->getFactory()
            ->createSearchStub()
            ->getGMTimeSlots($requestTransfer);
    }

    /**
     * @param int $idBranch
     * @return array
     */
    public function getGMSettings(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createStorageStub()
            ->getGMSettings($idBranch);
    }
}
