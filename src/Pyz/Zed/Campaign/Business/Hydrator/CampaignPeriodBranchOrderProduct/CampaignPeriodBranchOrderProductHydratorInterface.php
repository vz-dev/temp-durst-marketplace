<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 15:49
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;

interface CampaignPeriodBranchOrderProductHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void;
}
