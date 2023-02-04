<?php
/**
 * Durst - project - CampaignQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 11:58
 */

namespace Pyz\Zed\Campaign\Persistence;

use DateTime;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class CampaignQueryContainer
 * @package Pyz\Zed\Campaign\Persistence
 * @method CampaignPersistenceFactory getFactory()
 */
class CampaignQueryContainer extends AbstractQueryContainer implements CampaignQueryContainerInterface
{
    protected const POSTGRES_DATE_INTERVAL = '(%s - make_interval(days := %s))';

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery
     */
    public function queryCampaignPeriod(): DstCampaignPeriodQuery
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterialQuery
     */
    public function queryCampaignAdvertisingMaterial(): DstCampaignAdvertisingMaterialQuery
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery
     */
    public function queryCampaignPeriodBranchOrder(): DstCampaignPeriodBranchOrderQuery
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProductQuery
     */
    public function queryCampaignPeriodBranchOrderProduct(): DstCampaignPeriodBranchOrderProductQuery
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderProductQuery();
    }


    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriod $campaignPeriod
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodsForBranchQuery(
        int $idBranch
    ): DstCampaignPeriodQuery
    {
        $ordered = $this
            ->getOrderCampaignIdsForBranch(
                $idBranch
            );

        return $this
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                sprintf(
                    static::POSTGRES_DATE_INTERVAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME
                ) . ' >= ?',
                new DateTime('now')
            )
            ->filterByIdCampaignPeriod(
                $ordered,
                Criteria::NOT_IN
            )
            ->orderByCampaignStartDate();
    }

    /**
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodsForCampaignIdsQuery(
        array $campaignIds
    ): DstCampaignPeriodQuery
    {
        return $this
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD . ' IN ?', $campaignIds
            )
            ->orderByCampaignStartDate();
    }

    /**
     * @param int $idBranch
     * @return DstCampaignPeriodBranchOrderQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodBranchOrdersForCampaignIdsQuery(
        array $campaignIds,
        int $idBranch
    ): DstCampaignPeriodBranchOrderQuery
    {
        return $this
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
            ->filterByIsActive(true)
            ->orderByCampaignStartDate(
                Criteria::DESC
            )
            ->where(
                DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD . ' IN ?', $campaignIds
            )
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array|CampaignPeriodBranchOrderTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPeriodBranchOrdersForBranchQuery(
        int $idBranch
    ): DstCampaignPeriodBranchOrderQuery
    {
        return $this
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
            ->filterByIsActive(true)
            ->orderByCampaignStartDate(
                Criteria::DESC
            )
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @param int $idBranch
     * @return array|int[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getOrderCampaignIdsForBranch(
        int $idBranch
    ): array
    {
        $orders = $this
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
            ->filterByIsActive(
                true
            )
            ->endUse()
            ->filterByFkBranch(
                $idBranch
            )
            ->find();

        $result = [];

        foreach ($orders as $order) {
            $result[] = $order
                ->getFkCampaignPeriod();
        }

        return $result;
    }

}
