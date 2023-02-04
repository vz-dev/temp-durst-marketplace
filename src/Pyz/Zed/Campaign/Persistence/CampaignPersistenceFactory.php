<?php
/**
 * Durst - project - CampaignPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 11:54
 */

namespace Pyz\Zed\Campaign\Persistence;

use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class CampaignPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery
     */
    public function createCampaignPeriodQuery(): DstCampaignPeriodQuery
    {
        return DstCampaignPeriodQuery::create();
    }

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery
     */
    public function createCampaignAdvertisingMaterialQuery(): DstCampaignAdvertisingMaterialQuery
    {
        return DstCampaignAdvertisingMaterialQuery::create();
    }

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery
     */
    public function createCampaignPeriodBranchOrderQuery(): DstCampaignPeriodBranchOrderQuery
    {
        return DstCampaignPeriodBranchOrderQuery::create();
    }

    /**
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     */
    public function createCampaignPeriodBranchOrderProductQuery(): DstCampaignPeriodBranchOrderProductQuery
    {
        return DstCampaignPeriodBranchOrderProductQuery::create();
    }
}
