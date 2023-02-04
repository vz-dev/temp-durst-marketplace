<?php
/**
 * Durst - project - CampaignFactoryInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:08
 */

namespace Pyz\Zed\Campaign\Business;


use DateTime;
use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Generated\Shared\Transfer\MerchantCampaignOrderTransfer;
use Generated\Shared\Transfer\PossibleCampaignProductTransfer;
use Orm\Zed\Campaign\Persistence\Base\DstCampaignPeriod;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder;

interface CampaignFacadeInterface
{
    /**
     * Find a specific campaign period identified by its ID
     *
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function getCampaignPeriodById(
        int $idCampaignPeriod
    ): CampaignPeriodTransfer;

    /**
     * Find a specific material by its ID
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function getCampaignAdvertisingMaterialById(
        int $idCampaignAdvertisingMaterial
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * Find a specific material by its ID for the given campaign period
     *
     * @param int $idCampaignAdvertisingMaterial
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function getCampaignAdvertisingMaterialByIdForPeriod(
        int $idCampaignAdvertisingMaterial,
        int $idCampaignPeriod
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * Adds a campaign period to the database and returns the hydrated transfer with ID set
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function saveCampaignPeriod(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): CampaignPeriodTransfer;

    /**
     * Adds a campaign period branch order to the database and returns the hydrated transfer with ID set
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function saveCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * Update a campaign period branch order and returns the hydrated transfer
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function updateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * @return array|CampaignAdvertisingMaterialTransfer[]
     */
    public function getAllActiveCampaignAdvertisingMaterial(): array;

    /**
     * Adds an advertising material to the database and returns the hydrated transfer with ID set
     *
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function addCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * Activate a campaign period by its ID
     *
     * @param int $idCampaignPeriod
     * @return bool
     */
    public function activateCampaignPeriod(
        int $idCampaignPeriod
    ): bool;

    /**
     * Deactivate a campaign period by its ID
     *
     * @param int $idCampaignPeriod
     * @return bool
     */
    public function deactivateCampaignPeriod(
        int $idCampaignPeriod
    ): bool;

    /**
     * Activate a campaign advertising material by its ID
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function activateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool;

    /**
     * Deactivate a campaign advertising material by its ID
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function deactivateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool;

    /**
     * Get an array full of dates from active periods
     * Exclude the given campaign period
     *
     * @param int|null $idCampaignPeriod
     * @return array|string[]
     */
    public function getDatesWithCampaigns(
        ?int $idCampaignPeriod
    ): array;

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodValidatorInterface[]
     */
    public function getCampaignPeriodValidators(): array;

    /**
     * Get a list of products associated to the given campaign and branch
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return array|\Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer[]
     */
    public function getProductsForCampaignAndBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): array;

    /**
     * Get a specific branch order by the given ID
     *
     * @param int $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function getCampaignPeriodBranchOrderById(
        int $idCampaignPeriodBranchOrder
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * Return a list of hydrated campaign period transfers
     * Which are:
     * - active
     * - bookable
     *
     * @return array|CampaignPeriodTransfer[]
     */
    public function getCampaignPeriodList(): array;

    /**
     * Get a list of hydrated campaign period transfers
     * Which are:
     * - active
     * - bookable
     * - not already ordered by the given branch
     *
     * @param int $idBranch
     * @return array|CampaignPeriodTransfer[]
     */
    public function getAvailableCampaignPeriodsForBranch(
        int $idBranch
    ): array;

    /**
     * Get a list of hydrated campaign period transfers
     * Which are:
     * - active
     * - bookable
     *
     * @return array|DstCampaignPeriod[]
     */
    public function getAllAvailableCampaignPeriods(): array;

    /**
     * Get a list of hydrated campaign period branch order transfers
     * Which are
     * - active
     * - bookable
     * - already ordered by the given branch
     *
     * @param int $idBranch
     * @return array|CampaignPeriodBranchOrderTransfer[]
     */
    public function getCampaignPeriodBranchOrdersForBranch(
        int $idBranch
    ): array;

    /**
     * Entity to Transfer
     *
     * @param DstCampaignPeriod $campaignPeriod
     * @return CampaignPeriodTransfer
     */
    public function entityToTransfer(
        DstCampaignPeriod $campaignPeriod
    );

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder $branchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function entityToTransferCampaignPeriodBranchOrder(
        DstCampaignPeriodBranchOrder $branchOrder
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * Check, if the given campaign period was already booked by the given branch
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return bool
     */
    public function isCampaignPeriodOrderedByBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): bool;

    /**
     * Get either a new merchant campaign order transfer or the one identified by its ID
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param int|null $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderTransfer
     */
    public function getMerchantCampaignOrderById(
        int $idCampaignPeriod,
        int $idBranch,
        ?int $idCampaignPeriodBranchOrder = null
    ): MerchantCampaignOrderTransfer;

    /**
     * Transform a merchant campaign order (e.g. from Merchant Center)
     * To the corresponding campaign period branch order (used in Fridge)
     *
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function createCampaignPeriodBranchOrderFromMerchantCampaignOrder(
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * Get a campaign period branch order product identified by the given ID
     *
     * @param int $idCampaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     */
    public function getCampaignOrderProductById(
        int $idCampaignPeriodBranchOrderProduct
    ): CampaignPeriodBranchOrderProductTransfer;

    /**
     * Get a list of available products for the given campaign period and branch
     * The search is restricted by the given SKU (in parts) and the exceptions
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|\Generated\Shared\Transfer\PossibleCampaignProductTransfer[]
     */
    public function findAvailableProductsForCampaign(
        int $idCampaignPeriod,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array;

    /**
     * Get a list of available products for the given date range and branch
     * The search is restricted by the given SKU (in parts) and the exceptions
     *
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|PossibleCampaignProductTransfer[]
     */
    public function findAvailableProductsForDateRange(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array;

    /**
     * Get a possible product by its ID for the given branch
     *
     * @param int $idBranch
     * @param string $sku
     * @return \Generated\Shared\Transfer\PossibleCampaignProductTransfer
     */
    public function getProductBySkuForBranch(
        int $idBranch,
        string $sku
    ): PossibleCampaignProductTransfer;

    /**
     *
     * @return mixed
     */
    public function saveIsBookableForCampaign();
}
