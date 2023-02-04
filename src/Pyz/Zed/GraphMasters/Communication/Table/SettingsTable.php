<?php
/**
 * Durst - project - SettingsTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 16:56
 */

namespace Pyz\Zed\GraphMasters\Communication\Table;


use Orm\Zed\GraphMasters\Persistence\Base\DstGraphmastersSettings;
use Orm\Zed\GraphMasters\Persistence\Map\DstGraphmastersSettingsTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\GraphMasters\Communication\Controller\IndexController;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SettingsTable extends AbstractTable
{
    protected const COL_ID = '#';
    protected const COL_ACTIVE = 'aktiv?';
    protected const COL_BRANCH = 'Branch';
    protected const COL_DEPOT_API_ID = 'Depot Api Id';
    protected const COL_DEPOT_PATH = 'Depot-Path';
    protected const COL_ACTION = 'Action';

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * SettingsTable constructor.
     * @param GraphMastersQueryContainerInterface $queryContainer
     */
    public function __construct(GraphMastersQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstGraphmastersSettingsTableMap::COL_ID_GRAPHMASTERS_SETTINGS => static::COL_ID,
            DstGraphmastersSettingsTableMap::COL_IS_ACTIVE => static::COL_ACTIVE,
            DstGraphmastersSettingsTableMap::COL_FK_BRANCH => static::COL_BRANCH,
            DstGraphmastersSettingsTableMap::COL_DEPOT_API_ID => static::COL_DEPOT_API_ID,
            DstGraphmastersSettingsTableMap::COL_DEPOT_PATH => static::COL_DEPOT_PATH,
            static::COL_ACTION => static::COL_ACTION,
        ]);

        $config->setRawColumns([
            DstGraphmastersSettingsTableMap::COL_IS_ACTIVE,
            static::COL_ACTION,
        ]);

        $config->setSearchable([
        ]);

        $config->setSortable([
            DstGraphmastersSettingsTableMap::COL_ID_GRAPHMASTERS_SETTINGS,
            DstGraphmastersSettingsTableMap::COL_FK_BRANCH,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryGraphmastersSettings()
            ->useSpyBranchQuery()
            ->endUse()
            ->withColumn(SpyBranchTableMap::COL_NAME, static::COL_BRANCH);

        /** @var DstGraphmastersSettings[] $settings */
        $settings = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($settings as $setting) {
            $results[] = [
                DstGraphmastersSettingsTableMap::COL_ID_GRAPHMASTERS_SETTINGS => $setting->getIdGraphmastersSettings(),
                DstGraphmastersSettingsTableMap::COL_IS_ACTIVE => $this->formatBool($setting->getIsActive()),
                DstGraphmastersSettingsTableMap::COL_FK_BRANCH => $setting->getVirtualColumn(self::COL_BRANCH),
                DstGraphmastersSettingsTableMap::COL_DEPOT_API_ID => $setting->getDepotApiId(),
                DstGraphmastersSettingsTableMap::COL_DEPOT_PATH => $setting->getDepotPath(),
                static::COL_ACTION => $this->formatActionButtons($setting->getIdGraphmastersSettings()),
            ];
        }

        return $results;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function formatBool($value): string
    {
        if ($value) {
            return '<i style="color: #108548" class="fa fa-check"></i>';
        }

        return '<i style="color: #ed5565" class="fa fa-times red"></i>';
    }

    /**
     * @param int $idCredentials
     *
     * @return string
     */
    protected function formatActionButtons(int $idCredentials): string
    {
        $buttons = [];
        $buttons[] = $this
            ->generateEditButton(
                sprintf(
                    '%s?%s=%d',
                    IndexController::URL_EDIT,
                    IndexController::PARAM_ID_SETTINGS,
                    $idCredentials
                ),
                'Edit'
            );

        $buttons[] = $this
            ->generateRemoveButton(
                sprintf(
                    '%s?%s=%d',
                    IndexController::URL_REMOVE,
                    IndexController::PARAM_ID_SETTINGS,
                    $idCredentials
                ),
                'Delete'
            );

        return implode('', $buttons);
    }
}

