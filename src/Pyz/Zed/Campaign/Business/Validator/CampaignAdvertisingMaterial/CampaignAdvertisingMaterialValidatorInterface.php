<?php
/**
 * Durst - project - CampaignAdvertisingMaterialValidatorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 12:02
 */

namespace Pyz\Zed\Campaign\Business\Validator\CampaignAdvertisingMaterial;

use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;

interface CampaignAdvertisingMaterialValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return bool
     */
    public function isValid(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): bool;
}
