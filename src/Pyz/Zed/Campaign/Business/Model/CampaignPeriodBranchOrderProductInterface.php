<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 15:42
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;

interface CampaignPeriodBranchOrderProductInterface
{
    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return array|\Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer[]
     */
    public function getProductsForCampaignAndBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): array;

    /**
     * @param int $idCampaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     */
    public function getCampaignOrderProductById(
        int $idCampaignPeriodBranchOrderProduct
    ): CampaignPeriodBranchOrderProductTransfer;
}
