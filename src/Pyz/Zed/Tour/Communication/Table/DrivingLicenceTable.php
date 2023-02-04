<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 08.08.18
 * Time: 14:20
 */

namespace Pyz\Zed\Tour\Communication\Table;


use Orm\Zed\Tour\Persistence\Map\DstDrivingLicenceTableMap;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class DrivingLicenceTable extends AbstractTable
{
    const ACTION = 'Action';

    const UPDATE_DRIVING_LICENCE_URL = '/tour/driving-licence/edit';
    const DELETE_DRIVING_LICENCE_URL = '/tour/driving-licence/delete';

    const PARAM_ID_DRIVING_LICENCE = 'id';

    /**
     * @var TourQueryContainerInterface
     */
    protected $tourQueryContainer;

    /**
     * DrivingLicenceTable constructor.
     * @param TourQueryContainerInterface $vehicleQueryContainer
     */
    public function __construct(TourQueryContainerInterface $tourQueryContainer)
    {
        $this->tourQueryContainer = $tourQueryContainer;
    }

    /**
     * @param TableConfiguration $config
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config) : TableConfiguration
    {
        $config->setHeader([
            DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE => 'ID',
            DstDrivingLicenceTableMap::COL_NAME => 'Name',
            DstDrivingLicenceTableMap::COL_CODE => 'Code',
            DstDrivingLicenceTableMap::COL_DESCRIPTION => 'Beschreibung',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([self::ACTION]);

        $config->setSortable([
            DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE,
            DstDrivingLicenceTableMap::COL_NAME,
            DstDrivingLicenceTableMap::COL_CODE,
        ]);

        $config->setSearchable([
            DstDrivingLicenceTableMap::COL_NAME,
            DstDrivingLicenceTableMap::COL_CODE,
            DstDrivingLicenceTableMap::COL_DESCRIPTION,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array
     */
    protected function prepareData(TableConfiguration $config) : array
    {
        $query = $this->tourQueryContainer->queryDrivingLicence();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE => $item[DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE],
                DstDrivingLicenceTableMap::COL_NAME => $item[DstDrivingLicenceTableMap::COL_NAME],
                DstDrivingLicenceTableMap::COL_CODE => $item[DstDrivingLicenceTableMap::COL_CODE],
                DstDrivingLicenceTableMap::COL_DESCRIPTION => $item[DstDrivingLicenceTableMap::COL_DESCRIPTION],

                self::ACTION => implode(' ', $this->createActionButtons($item)),
            ];
        }

        return $results;
    }

    /**
     * @param array $drivingLicence
     * @return array
     */
    public function createActionButtons(array $drivingLicence) : array
    {
        $urls = [];


        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_DRIVING_LICENCE_URL, [
                self::PARAM_ID_DRIVING_LICENCE => $drivingLicence[DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE],
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(self::DELETE_DRIVING_LICENCE_URL, 'Delete', [
            self::PARAM_ID_DRIVING_LICENCE => $drivingLicence[DstDrivingLicenceTableMap::COL_ID_DRIVING_LICENCE],
        ]);

        return $urls;
    }
}
