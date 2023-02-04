<?php
/**
 * Durst - project - MerchantCampaignOrderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.06.21
 * Time: 14:58
 */

namespace Pyz\Zed\Campaign\Business\Model;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Generated\Shared\Transfer\MerchantCampaignOrderTransfer;

interface MerchantCampaignOrderInterface
{
    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param int|null $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderTransfer
     */
    public function getMerchantCampaignOrderById(
        int $idCampaignPeriod,
        int $idBranch,
        ?int $idCampaignPeriodBranchOrder = null
    ): MerchantCampaignOrderTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     */
    public function createCampaignPeriodBranchOrderFromMerchantCampaignOrder(
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): CampaignPeriodBranchOrderTransfer;
}
