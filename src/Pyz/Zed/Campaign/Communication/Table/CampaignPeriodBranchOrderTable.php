<?php
/**
 * Durst - project - CampaignPeriodBranchOrderTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 16:56
 */

namespace Pyz\Zed\Campaign\Communication\Table;


use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodBranchOrderTableMap;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\Controller\CampaignPeriodBranchOrderController;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CampaignPeriodBranchOrderTable extends AbstractTable
{
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ID = 'ID';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_NAME = 'Name';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BRANCH = 'Branch';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_START = 'Start date';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_END = 'End date';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_PRODUCTS = 'Products';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BOOKABLE = 'Bookable';
    public const HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ACTION = 'Action';

    protected const KEY_BRANCH_NAME = 'branch_name';
    protected const KEY_CAMPAIGN_PERIOD_NAME = 'campaign_period_name';
    protected const KEY_CAMPAIGN_PERIOD_START = 'campaign_period_start';
    protected const KEY_CAMPAIGN_PERIOD_END = 'campaign_period_end';

    protected const IS_ACTIVE = '<i class="fa fa-check" style="color: #3adb76"></i>';
    protected const IS_INACTIVE = '<i class="fa fa-times" style="color: #851010"></i>';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * CampaignPeriodBranchOrderTable constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        CampaignFacadeInterface $facade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader(
                [
                    DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ID,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_NAME,
                    SpyBranchTableMap::COL_NAME => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BRANCH,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_START,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_END,
                    static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_PRODUCTS => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_PRODUCTS,
                    static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BOOKABLE => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BOOKABLE,
                    static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ACTION => static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ACTION
                ]
            );

        $config
            ->setRawColumns(
                [
                    static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BOOKABLE,
                    static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ACTION
                ]
            );

        $config
            ->setSearchable(
                [
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME,
                    SpyBranchTableMap::COL_NAME
                ]
            );

        $config
            ->setSortable(
                [
                    DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME,
                    SpyBranchTableMap::COL_NAME,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE
                ]
            );

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->joinWithSpyBranch()
            ->joinWithDstCampaignPeriod()
            ->addAsColumn(
                static::KEY_BRANCH_NAME,
                SpyBranchTableMap::COL_NAME
            )
            ->addAsColumn(
                static::KEY_CAMPAIGN_PERIOD_NAME,
                DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME
            )
            ->addAsColumn(
                static::KEY_CAMPAIGN_PERIOD_START,
                DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE
            )
            ->addAsColumn(
                static::KEY_CAMPAIGN_PERIOD_END,
                DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE
            );

        $queryResults = $this
            ->runQuery(
                $query,
                $config
            );

        $result = [];

        foreach ($queryResults as $queryResult) {
            $result[] = [
                DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER => $queryResult[DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME => $queryResult[static::KEY_CAMPAIGN_PERIOD_NAME],
                SpyBranchTableMap::COL_NAME => $queryResult[static::KEY_BRANCH_NAME],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE => $queryResult[static::KEY_CAMPAIGN_PERIOD_START],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE => $queryResult[static::KEY_CAMPAIGN_PERIOD_END],
                static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_PRODUCTS => $this->countBranchOrderProducts(
                    $queryResult[DstCampaignPeriodBranchOrderTableMap::COL_FK_CAMPAIGN_PERIOD],
                    $queryResult[DstCampaignPeriodBranchOrderTableMap::COL_FK_BRANCH]
                ),
                static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_BOOKABLE => $this->getBookable($queryResult),
                static::HEADER_CAMPAIGN_PERIOD_BRANCH_ORDER_ACTION => $this->createActionButtons($queryResult)
            ];
        }

        return $result;
    }

    /**
     * @param array $queryResult
     * @return string
     */
    protected function getBookable(array $queryResult): string
    {
        $campaignPeriodTransfer = $this
            ->getCampaignPeriodTransfer(
                $queryResult
            );

        return $this
            ->getIsActive(
                $campaignPeriodTransfer
                    ->getBookable()
            );
    }

    /**
     * @param array $queryResult
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    protected function getCampaignPeriodTransfer(array $queryResult): CampaignPeriodTransfer
    {
        $idCampaignPeriod = $queryResult[DstCampaignPeriodBranchOrderTableMap::COL_FK_CAMPAIGN_PERIOD];

        return $this
            ->facade
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * @param bool $isActive
     * @return string
     */
    protected function getIsActive(bool $isActive): string
    {
        if ($isActive === true) {
            return static::IS_ACTIVE;
        }

        return static::IS_INACTIVE;
    }

    /**
     * @param array $campaignPeriodBranchOrder
     * @return array|string[]
     */
    protected function createActionButtons(array $campaignPeriodBranchOrder): array
    {
        $urls = [];

        $urls[] = $this
            ->generateViewButton(
                Url::generate(
                    CampaignPeriodBranchOrderController::URL_VIEW,
                    [
                        CampaignPeriodBranchOrderController::PARAM_ID_CAMPAIGN_PERIOD_BRANCH_ORDER => $campaignPeriodBranchOrder[DstCampaignPeriodBranchOrderTableMap::COL_ID_CAMPAIGN_PERIOD_BRANCH_ORDER]
                    ]
                ),
                'View'
            );

        return $urls;
    }

    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return int
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function countBranchOrderProducts(
        int $idCampaignPeriod,
        int $idBranch
    ): int
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProduct()
            ->useDstCampaignPeriodBranchOrderQuery()
                ->filterByFkBranch(
                    $idBranch
                )
                ->filterByFkCampaignPeriod(
                    $idCampaignPeriod
                )
            ->endUse()
            ->count();
    }
}
