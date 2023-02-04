<?php
/**
 * Durst - project - CampaignPeriodBranchOrderHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 15:13
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;

interface CampaignPeriodBranchOrderHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return void
     */
    public function hydrateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): void;
}
