<?php
/**
 * Durst - project - CampaignAdvertisingMaterialInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 09:39
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;

interface CampaignAdvertisingMaterialInterface
{
    /**
     * @param int $idCampaignAdvertisingMaterial
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function getCampaignAdvertisingMaterialByIdForPeriod(
        int $idCampaignAdvertisingMaterial,
        int $idCampaignPeriod
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * @param int $idCampaignAdvertisingMaterial
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function getCampaignAdvertisingMaterialById(
        int $idCampaignAdvertisingMaterial
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function addCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): CampaignAdvertisingMaterialTransfer;

    /**
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function activateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool;

    /**
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function deactivateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool;

    /**
     * @return array|CampaignAdvertisingMaterialTransfer[]
     */
    public function getAllActiveCampaignAdvertisingMaterial(): array;
}
