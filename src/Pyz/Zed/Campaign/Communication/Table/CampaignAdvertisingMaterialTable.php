<?php
/**
 * Durst - project - CampaignAdvertisingMaterialTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 14:54
 */

namespace Pyz\Zed\Campaign\Communication\Table;

use Orm\Zed\Campaign\Persistence\Map\DstCampaignAdvertisingMaterialTableMap;
use Pyz\Zed\Campaign\Communication\Controller\CampaignAdvertisingMaterialController;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CampaignAdvertisingMaterialTable extends AbstractTable
{
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ID = 'ID';
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_NAME = 'Name';
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION = 'Description';
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME = 'Lead-Time';
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTIVE = 'Active';
    public const HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTION = 'Action';

    protected const IS_ACTIVE = '<i class="fa fa-check" style="color: #3adb76"></i>';
    protected const IS_INACTIVE = '<i class="fa fa-times" style="color: #851010"></i>';

    protected const LEAD_TIME_TEMPLATE = '%d week(s)';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * CampaignAdvertisingMaterialTable constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
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
                    DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ID,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_NAME,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME,
                    DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTIVE,
                    static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTION => static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTION
                ]
            );

        $config
            ->setRawColumns(
                [
                    DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE,
                    static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTION
                ]
            );

        $config
            ->setSearchable(
                [
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION
                ]
            );

        $config
            ->setSortable(
                [
                    DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME,
                    DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME,
                    DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE
                ]
            );

        $config
            ->setDefaultSortField(
                DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME,
                TableConfiguration::SORT_ASC
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
            ->queryCampaignAdvertisingMaterial();

        $queryResults = $this
            ->runQuery(
                $query,
                $config
            );

        $results = [];

        foreach ($queryResults as $queryResult) {
            $results[] = [
                DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL => $queryResult[DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL],
                DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME => $queryResult[DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_NAME],
                DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION => $queryResult[DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION],
                DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME => sprintf(
                    static::LEAD_TIME_TEMPLATE,
                    $queryResult[DstCampaignAdvertisingMaterialTableMap::COL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME]
                ),
                DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE => $this->getIsActive($queryResult[DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE]),
                static::HEADER_CAMPAIGN_ADVERTISING_MATERIAL_ACTION => implode(' ', $this->createActionButtons($queryResult))
            ];
        }

        return $results;
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
     * @param array $campaignAdvertisingMaterial
     * @return array
     */
    protected function createActionButtons(array $campaignAdvertisingMaterial): array
    {
        $urls = [];

        $urls[] = $this
            ->generateEditButton(
                Url::generate(
                    CampaignAdvertisingMaterialController::URL_EDIT,
                    [
                        CampaignAdvertisingMaterialController::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL => $campaignAdvertisingMaterial[DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL]
                    ]
                ),
                'Edit'
            );

        if ($campaignAdvertisingMaterial[DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE] === true) {
            $urls[] = $this
                ->generateRemoveButton(
                    Url::generate(
                        CampaignAdvertisingMaterialController::URL_DEACTIVATE,
                        [
                            CampaignAdvertisingMaterialController::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL => $campaignAdvertisingMaterial[DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL]
                        ]
                    ),
                    'Delete'
                );
        }

        if ($campaignAdvertisingMaterial[DstCampaignAdvertisingMaterialTableMap::COL_IS_ACTIVE] === false) {
            $urls[] = $this
                ->generateViewButton(
                    Url::generate(
                        CampaignAdvertisingMaterialController::URL_ACTIVATE,
                        [
                            CampaignAdvertisingMaterialController::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL => $campaignAdvertisingMaterial[DstCampaignAdvertisingMaterialTableMap::COL_ID_CAMPAIGN_ADVERTISING_MATERIAL]
                        ]
                    ),
                    'Restore'
                );
        }

        return $urls;
    }
}
