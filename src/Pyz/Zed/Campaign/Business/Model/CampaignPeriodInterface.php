<?php
/**
 * Durst - project - CampaignPeriodInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 11:53
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignPeriodTransfer;

interface CampaignPeriodInterface
{
    /**
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function getCampaignPeriodById(int $idCampaignPeriod): CampaignPeriodTransfer;

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function saveCampaignPeriod(CampaignPeriodTransfer $campaignPeriodTransfer): CampaignPeriodTransfer;

    /**
     * @param int $idCampaignPeriod
     * @return bool
     */
    public function activateCampaignPeriod(
        int $idCampaignPeriod
    ): bool;

    /**
     * @param int $idCampaignPeriod
     * @return bool
     */
    public function deactivateCampaignPeriod(
        int $idCampaignPeriod
    ): bool;

    /**
     * @param int|null $idCampaignPeriod
     * @return array
     */
    public function getDatesWithCampaigns(
        ?int $idCampaignPeriod
    ): array;

    /**
     * @return array|CampaignPeriodTransfer[]
     */
    public function getCampaignPeriodList(): array;

    /**
     * @param int $idBranch
     * @return array|CampaignPeriodTransfer[]
     */
    public function getAvailableCampaignPeriodsForBranch(
        int $idBranch
    ): array;

    /**
     * @return array|CampaignPeriodTransfer[]
     */
    public function getAllAvailableCampaignPeriods(): array;
}
