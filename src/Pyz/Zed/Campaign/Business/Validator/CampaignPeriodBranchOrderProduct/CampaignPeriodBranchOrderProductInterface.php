<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.07.21
 * Time: 15:06
 */

namespace Pyz\Zed\Campaign\Business\Validator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;

interface CampaignPeriodBranchOrderProductInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return bool
     */
    public function isValid(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): bool;
}
