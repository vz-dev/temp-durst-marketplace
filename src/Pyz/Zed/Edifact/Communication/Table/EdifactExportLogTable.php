<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 15:29
 */

namespace Pyz\Zed\Edifact\Communication\Table;

use DateTime;
use DateTimeZone;
use Orm\Zed\Edifact\Persistence\DstEdifactExportLog;
use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Pyz\Zed\Edifact\EdifactConfig;
use Pyz\Zed\Edifact\Persistence\EdifactQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class EdifactExportLogTable extends AbstractTable
{
    protected const DATE_FORMAT = 'd.m.y H:i';

    /**
     * @var EdifactQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var EdifactConfig
     */
    protected $edifactConfig;

    /**
     * EdifactExportLogTable constructor.
     *
     * @param EdifactQueryContainerInterface $queryContainer
     * @param EdifactConfig $edifactConfig
     */
    public function __construct(
        EdifactQueryContainerInterface $queryContainer,
        EdifactConfig $edifactConfig
    ) {
        $this->queryContainer = $queryContainer;
        $this->edifactConfig = $edifactConfig;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config
            ->setHeader([
                DstEdifactExportLogTableMap::COL_ID_EDIFACT_EXPORT_LOG => 'ID',
                DstConcreteTourTableMap::COL_TOUR_REFERENCE => 'Referenz',
                DstEdifactExportLogTableMap::COL_LOG_LEVEL => 'Level',
                DstEdifactExportLogTableMap::COL_STATUS_CODE => 'Code',
                DstEdifactExportLogTableMap::COL_REASON_PHRASE => 'Nachricht',
                DstEdifactExportLogTableMap::COL_EXPORT_TYPE => 'Export',
                DstEdifactExportLogTableMap::COL_ENDPOINT_URL => 'URL',
                DstEdifactExportLogTableMap::COL_CREATED_AT => 'Datum',
                DstEdifactExportLogTableMap::COL_EDIFACT_MESSAGE => 'EDIFact',
            ]);

        $config->setSortable([
            DstEdifactExportLogTableMap::COL_ID_EDIFACT_EXPORT_LOG,
            DstEdifactExportLogTableMap::COL_CREATED_AT,
            DstEdifactExportLogTableMap::COL_STATUS_CODE,
            DstEdifactExportLogTableMap::COL_ENDPOINT_URL,
            DstEdifactExportLogTableMap::COL_EXPORT_TYPE,
        ]);

        $config->setSearchable([
            DstEdifactExportLogTableMap::COL_EDIFACT_MESSAGE,
            DstEdifactExportLogTableMap::COL_CREATED_AT,
            DstEdifactExportLogTableMap::COL_ENDPOINT_URL,
            DstEdifactExportLogTableMap::COL_EXPORT_TYPE,
        ]);

        $config->setDefaultSortField(
            DstEdifactExportLogTableMap::COL_ID_EDIFACT_EXPORT_LOG,
            TableConfiguration::SORT_DESC
        );

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
            ->queryEdifactExportLog()
            ->leftJoinDstConcreteTour();

        $results = $this
            ->runQuery(
                $query,
                $config,
                true
            );

        $tableResult = [];

        /** @var DstEdifactExportLog $result */
        foreach ($results as $result) {
            $tourReference = 'n/a';

            if ($result->getDstConcreteTour() !== null) {
                $tourReference = $result
                    ->getDstConcreteTour()
                    ->getTourReference();
            } else if ($result->getDstGraphmastersTour() !== null) {
                $tourReference = $result
                    ->getDstGraphmastersTour()
                    ->getReference();
            }

            $tableResult[] = [
                DstEdifactExportLogTableMap::COL_ID_EDIFACT_EXPORT_LOG => $result->getIdEdifactExportLog(),
                DstConcreteTourTableMap::COL_TOUR_REFERENCE => $tourReference,
                DstEdifactExportLogTableMap::COL_LOG_LEVEL => $result->getLogLevel(),
                DstEdifactExportLogTableMap::COL_STATUS_CODE => $result->getStatusCode(),
                DstEdifactExportLogTableMap::COL_REASON_PHRASE => $result->getReasonPhrase(),
                DstEdifactExportLogTableMap::COL_EXPORT_TYPE => $result->getExportType(),
                DstEdifactExportLogTableMap::COL_ENDPOINT_URL => $result->getEndpointUrl(),
                DstEdifactExportLogTableMap::COL_CREATED_AT => $this->getDateTimeString($result->getCreatedAt()),
                DstEdifactExportLogTableMap::COL_EDIFACT_MESSAGE => $result->getEdifactMessage(),
            ];
        }

        return $tableResult;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return string
     */
    protected function getDateTimeString(DateTime $dateTime): string
    {
        $dateTime->setTimezone(
            new DateTimeZone(
                $this->edifactConfig->getProjectTimeZone()
            )
        );

        return $dateTime->format(self::DATE_FORMAT);
    }
}
