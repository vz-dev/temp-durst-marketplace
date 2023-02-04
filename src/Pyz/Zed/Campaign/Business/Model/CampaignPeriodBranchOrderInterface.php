<?php
/**
 * Durst - project - CampaignPeriodBranchOrderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 15:42
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;

interface CampaignPeriodBranchOrderInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function saveCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function updateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * @param int $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function getCampaignPeriodBranchOrderById(
        int $idCampaignPeriodBranchOrder
    ): CampaignPeriodBranchOrderTransfer;

    /**
     * @param int $idBranch
     * @return array|CampaignPeriodBranchOrderTransfer[]
     */
    public function getCampaignPeriodBranchOrdersForBranch(
        int $idBranch
    ): array;

    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return bool
     */
    public function isCampaignPeriodOrderedByBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): bool;
}
