<?php
/**
 * Durst - project - CampaignPeriodTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:17
 */

namespace Pyz\Zed\Campaign\Communication\Table;

use Orm\Zed\Campaign\Persistence\Map\DstCampaignAdvertisingMaterialTableMap;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodCampaignAdvertisingMaterialTableMap;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodTableMap;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\Controller\CampaignPeriodController;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CampaignPeriodTable extends AbstractTable
{
    public const HEADER_CAMPAIGN_PERIOD_ID = 'ID';
    public const HEADER_CAMPAIGN_PERIOD_NAME = 'Name';
    public const HEADER_CAMPAIGN_PERIOD_DESCRIPTION = 'Description';
    public const HEADER_CAMPAIGN_PERIOD_START_DATE = 'Start-Date';
    public const HEADER_CAMPAIGN_PERIOD_END_DATE = 'End-Date';
    public const HEADER_CAMPAIGN_PERIOD_ADVERTISING_MATERIAL = 'Advertising Material';
    public const HEADER_CAMPAIGN_PERIOD_LEAD_TIME = 'Lead-Time';
    public const HEADER_CAMPAIGN_PERIOD_BOOKABLE = 'Bookable';
    public const HEADER_CAMPAIGN_PERIOD_ACTIVE = 'Active';
    public const HEADER_CAMPAIGN_PERIOD_ACTION = 'Action';

    protected const IS_ACTIVE = '<i class="fa fa-check" style="color: #3adb76"></i>';
    protected const IS_INACTIVE = '<i class="fa fa-times" style="color: #851010"></i>';

    protected const LEAD_TIME_WEEKS_TEMPLATE = '%d week(s)';
    protected const LEAD_TIME_DAYS_TEMPLATE = '%d day(s)';
    protected const DAYS_LEFT_TEMPLATE = '%d day(s) left';

    protected const DATE_FORMAT = 'Y-m-d';

    protected const TABLE_TEMPLATE = '<table width="100%%">%s</table>';
    protected const ROW_TEMPLATE = '<tr><td><strong>%s</strong> <small>(%s)</small><br><small><strong>%s</strong> (%s)</small></td><td>%s</td></tr><tr><td colspan="2"><hr/></td> </tr>';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * @var \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    protected $campaignPeriodTransfer;

    /**
     * CampaignPeriodTable constructor.
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
                    DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD => static::HEADER_CAMPAIGN_PERIOD_ID,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME => static::HEADER_CAMPAIGN_PERIOD_NAME,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_DESCRIPTION => static::HEADER_CAMPAIGN_PERIOD_DESCRIPTION,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE => static::HEADER_CAMPAIGN_PERIOD_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE => static::HEADER_CAMPAIGN_PERIOD_END_DATE,
                    DstCampaignPeriodCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL => static::HEADER_CAMPAIGN_PERIOD_ADVERTISING_MATERIAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME => static::HEADER_CAMPAIGN_PERIOD_LEAD_TIME,
                    static::HEADER_CAMPAIGN_PERIOD_BOOKABLE => static::HEADER_CAMPAIGN_PERIOD_BOOKABLE,
                    DstCampaignPeriodTableMap::COL_IS_ACTIVE => static::HEADER_CAMPAIGN_PERIOD_ACTIVE,
                    static::HEADER_CAMPAIGN_PERIOD_ACTION => static::HEADER_CAMPAIGN_PERIOD_ACTION
                ]
            );

        $config
            ->setRawColumns(
                [
                    static::HEADER_CAMPAIGN_PERIOD_BOOKABLE,
                    DstCampaignPeriodTableMap::COL_IS_ACTIVE,
                    DstCampaignPeriodCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL,
                    static::HEADER_CAMPAIGN_PERIOD_ACTION
                ]
            );

        $config
            ->setSearchable(
                [
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_DESCRIPTION,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME
                ]
            );

        $config
            ->setSortable(
                [
                    DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE
                ]
            );

        $config
            ->setDefaultSortField(
                DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                TableConfiguration::SORT_DESC
            );

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->useDstCampaignPeriodCampaignAdvertisingMaterialQuery()
                ->useDstCampaignAdvertisingMaterialQuery()
                ->endUse()
            ->endUse()
            ->groupByIdCampaignPeriod();

        $queryResults = $this
            ->runQuery(
                $query,
                $config
            );

        $results = [];

        foreach ($queryResults as $queryResult) {
            $this
                ->setCampaignPeriodTransfer($queryResult);

            $results[] = [
                DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD => $queryResult[DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME => $queryResult[DstCampaignPeriodTableMap::COL_CAMPAIGN_NAME],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_DESCRIPTION => $queryResult[DstCampaignPeriodTableMap::COL_CAMPAIGN_DESCRIPTION],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE => $queryResult[DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE],
                DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE => $queryResult[DstCampaignPeriodTableMap::COL_CAMPAIGN_END_DATE],
                DstCampaignPeriodCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL => $this->getCampaignAdvertisingMaterialTable(),
                DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME => sprintf(
                    static::LEAD_TIME_DAYS_TEMPLATE,
                    $queryResult[DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME]
                ),
                static::HEADER_CAMPAIGN_PERIOD_BOOKABLE => $this->getBookable(),
                DstCampaignPeriodTableMap::COL_IS_ACTIVE => $this->getIsActive($queryResult[DstCampaignPeriodTableMap::COL_IS_ACTIVE]),
                static::HEADER_CAMPAIGN_PERIOD_ACTION => implode(' ', $this->createActionButtons())
            ];
        }

        return $results;
    }

    /**
     * @param array $queryResult
     * @return void
     */
    protected function setCampaignPeriodTransfer(array $queryResult): void
    {
        $idCampaignPeriod = $queryResult[DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD];

        $this->campaignPeriodTransfer = $this
            ->facade
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * @return string
     */
    protected function getBookable(): string
    {
        return $this
            ->getIsActive(
                $this
                    ->campaignPeriodTransfer
                    ->getBookable()
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
     * @return string
     */
    protected function getCampaignAdvertisingMaterialTable(): string
    {
        $materials = $this
            ->campaignPeriodTransfer
            ->getAssignedCampaignAdvertisingMaterials();

        $result = '';

        if ($materials->count() > 0) {
            $result .= static::TABLE_TEMPLATE;

            $table = '';

            foreach ($materials as $material) {
                $row = static::ROW_TEMPLATE;

                $table .= sprintf(
                    $row,
                    $material
                        ->getCampaignAdvertisingMaterialName(),
                    sprintf(
                        static::LEAD_TIME_WEEKS_TEMPLATE,
                        $material
                            ->getCampaignAdvertisingMaterialLeadTime()
                    ),
                    $material
                        ->getCampaignAdvertisingMaterialEndDate()
                        ->format(
                            static::DATE_FORMAT
                        ),
                    sprintf(
                        static::DAYS_LEFT_TEMPLATE,
                        $material
                            ->getDaysLeft()
                    ),
                    $this
                        ->getIsActive(
                            $material
                                ->getIsActive()
                        )
                );
            }

            $result = sprintf(
                $result,
                $table
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function createActionButtons(): array
    {
        $urls = [];

        if ($this->campaignPeriodTransfer->getBookable() === true) {
            $urls[] = $this
                ->generateEditButton(
                    Url::generate(
                        CampaignPeriodController::URL_EDIT,
                        [
                            CampaignPeriodController::PARAM_ID_CAMPAIGN_PERIOD => $this->campaignPeriodTransfer->getIdCampaignPeriod()
                        ]
                    ),
                    'Edit'
                );
        }

        if ($this->campaignPeriodTransfer->getBookable() !== true) {
            $urls[] = $this
                ->generateViewButton(
                    Url::generate(
                        CampaignPeriodController::URL_VIEW,
                        [
                            CampaignPeriodController::PARAM_ID_CAMPAIGN_PERIOD => $this->campaignPeriodTransfer->getIdCampaignPeriod()
                        ]
                    ),
                    'View'
                );
        }

        if ($this->campaignPeriodTransfer->getIsActive() === true) {
            $urls[] = $this
                ->generateRemoveButton(
                    Url::generate(
                        CampaignPeriodController::URL_DEACTIVATE,
                        [
                            CampaignPeriodController::PARAM_ID_CAMPAIGN_PERIOD => $this->campaignPeriodTransfer->getIdCampaignPeriod()
                        ]
                    ),
                    'Delete'
                );
        }

        if ($this->campaignPeriodTransfer->getIsActive() === false) {
            $urls[] = $this
                ->generateViewButton(
                    Url::generate(
                        CampaignPeriodController::URL_ACTIVATE,
                        [
                            CampaignPeriodController::PARAM_ID_CAMPAIGN_PERIOD => $this->campaignPeriodTransfer->getIdCampaignPeriod()
                        ]
                    ),
                    'Restore'
                );
        }

        return $urls;
    }
}
