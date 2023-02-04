<?php
/**
 * Durst - project - CampaignPeriodHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 09:51
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod;

use Generated\Shared\Transfer\CampaignPeriodTransfer;

interface CampaignPeriodHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     */
    public function hydrateCampaignPeriod(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): void;
}
