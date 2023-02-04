<?php
/**
 * Durst - project - ReleaseTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 10:27
 */

namespace Pyz\Zed\DriverApp\Communication\Table;

use Orm\Zed\DriverApp\Persistence\DstDriverAppRelease;
use Orm\Zed\DriverApp\Persistence\Map\DstDriverAppReleaseTableMap;
use Pyz\Zed\DriverApp\Communication\Controller\IndexController;
use Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class ReleaseTable extends AbstractTable
{
    protected const DATE_FORMAT = 'd.m.Y';
    protected const COL_ACTION = 'COL_ACTION';

    /**
     * @var \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * ReleaseTable constructor.
     *
     * @param \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface $queryContainer
     */
    public function __construct(DriverAppQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstDriverAppReleaseTableMap::COL_VERSION => 'Version',
            DstDriverAppReleaseTableMap::COL_PATCH_NOTES => 'Patch Notes',
            DstDriverAppReleaseTableMap::COL_CREATED_AT => 'Erstellt am',
            DstDriverAppReleaseTableMap::COL_UPDATED_AT => 'Geändert am',
            self::COL_ACTION => 'Aktion',
        ]);

        $config->setRawColumns([
            self::COL_ACTION,
        ]);

        $config->setSearchable([
            DstDriverAppReleaseTableMap::COL_VERSION,
        ]);

        $config->setSortable([
            DstDriverAppReleaseTableMap::COL_CREATED_AT,
            DstDriverAppReleaseTableMap::COL_UPDATED_AT,
        ]);

        $config->setDefaultSortField(
            DstDriverAppReleaseTableMap::COL_CREATED_AT,
            TableConfiguration::SORT_DESC
        );

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryDriverAppRelease();
        $queryResults = $this->runQuery($query, $config, true);

        $results = [];
        /** @var \Orm\Zed\DriverApp\Persistence\DstDriverAppRelease $appReleaseEntity */
        foreach ($queryResults as $appReleaseEntity) {
            $results[] = [
                DstDriverAppReleaseTableMap::COL_VERSION => $appReleaseEntity->getVersion(),
                DstDriverAppReleaseTableMap::COL_PATCH_NOTES => $appReleaseEntity->getPatchNotes(),
                DstDriverAppReleaseTableMap::COL_CREATED_AT => $appReleaseEntity->getCreatedAt()->format(self::DATE_FORMAT),
                DstDriverAppReleaseTableMap::COL_UPDATED_AT => $appReleaseEntity->getUpdatedAt()->format(self::DATE_FORMAT),
                self::COL_ACTION => implode(' ', $this->createActionButtons($appReleaseEntity)),
            ];
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\DriverApp\Persistence\DstDriverAppRelease $appReleaseEntity
     *
     * @return array
     */
    public function createActionButtons(DstDriverAppRelease $appReleaseEntity)
    {
        $urls = [];

        $urls[] = $this->generateRemoveButton(
            Url::generate(IndexController::URL_DELETE, [
                IndexController::PARAM_ID_RELEASE => $appReleaseEntity->getIdDriverAppRelease(),
            ]),
            'Löschen'
        );

        return $urls;
    }
}
