<?php
/**
 * Durst - project - CampaignAdvertisingMaterialHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 09:52
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial;

use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;

interface CampaignAdvertisingMaterialHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     */
    public function hydrateCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): void;
}
