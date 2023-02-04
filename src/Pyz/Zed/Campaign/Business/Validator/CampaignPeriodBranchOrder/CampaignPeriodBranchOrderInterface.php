<?php
/**
 * Durst - project - CampaignPeriodBranchOrderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.07.21
 * Time: 15:03
 */

namespace Pyz\Zed\Campaign\Business\Validator\CampaignPeriodBranchOrder;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;

interface CampaignPeriodBranchOrderInterface
{
    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @return bool
     */
    public function isValid(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer
    ): bool;
}
