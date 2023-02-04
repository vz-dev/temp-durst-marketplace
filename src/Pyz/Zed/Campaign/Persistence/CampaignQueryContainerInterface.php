<?php
/**
 * Durst - project - CampaignQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 11:57
 */

namespace Pyz\Zed\Campaign\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface CampaignQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery
     */
    public function queryCampaignPeriod(): DstCampaignPeriodQuery;

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery
     */
    public function queryCampaignAdvertisingMaterial(): DstCampaignAdvertisingMaterialQuery;

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery
     */
    public function queryCampaignPeriodBranchOrder(): DstCampaignPeriodBranchOrderQuery;

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     */
    public function queryCampaignPeriodBranchOrderProduct(): DstCampaignPeriodBranchOrderProductQuery;

    /**
     * Get a list of hydrated campaign period branch order transfers
     * Which are
     * - active
     * - bookable
     * - already ordered by the given branch
     *
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     */
    public function getCampaignPeriodBranchOrdersForBranchQuery(
        int $idBranch
    ): DstCampaignPeriodBranchOrderQuery;

    /**
     * Get a list of hydrated campaign period branch order transfers
     * based on some campaign ids
     *
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     */
    public function getAvailableCampaignPeriodBranchOrdersForCampaignIdsQuery(
        array $campaignIds,
        int $idBranch
    );
}
