<?php
/**
 * Durst - project - LogTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 18.11.20
 * Time: 17:13
 */

namespace Pyz\Zed\Integra\Communication\Table;

use DateTime;
use DateTimeZone;
use Orm\Zed\Integra\Persistence\Map\PyzIntegraLogTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class LogTable extends AbstractTable
{
    protected const COL_LEVEL = 'Level';
    protected const COL_BRANCH = 'Branch';
    protected const COL_MESSAGE = 'Nachricht';
    protected const COL_TIMESTAMP = 'Zeitstempel';

    protected const TIME_FORMAT = 'd.m.y H:i:s';

    /**
     * @var \Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Integra\IntegraConfig
     */
    protected $integraConfig;

    /**
     * LogTable constructor.
     *
     * @param \Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Integra\IntegraConfig $integraConfig
     */
    public function __construct(
        IntegraQueryContainerInterface $queryContainer,
        IntegraConfig $integraConfig
    ) {
        $this->queryContainer = $queryContainer;
        $this->integraConfig = $integraConfig;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            PyzIntegraLogTableMap::COL_LEVEL => static::COL_LEVEL,
            PyzIntegraLogTableMap::COL_FK_BRANCH => static::COL_BRANCH,
            PyzIntegraLogTableMap::COL_MESSAGE => static::COL_MESSAGE,
            PyzIntegraLogTableMap::COL_CREATED_AT => static::COL_TIMESTAMP,
        ]);

        $config->setRawColumns([
            PyzIntegraLogTableMap::COL_LEVEL,
        ]);

        $config->setDefaultSortField(PyzIntegraLogTableMap::COL_CREATED_AT, TableConfiguration::SORT_DESC);

        $config->setSearchable([
            PyzIntegraLogTableMap::COL_CREATED_AT,
        ]);

        $config->setSortable([
            PyzIntegraLogTableMap::COL_CREATED_AT,
            PyzIntegraLogTableMap::COL_LEVEL,
            PyzIntegraLogTableMap::COL_FK_BRANCH,
        ]);

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
            ->queryIntegraLog()
            ->useSpyBranchQuery()
            ->endUse()
            ->withColumn(SpyBranchTableMap::COL_NAME, static::COL_BRANCH);

        /** @var \Orm\Zed\Integra\Persistence\PyzIntegraLog[] $logEntries */
        $logEntries = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($logEntries as $logEntry) {
            $results[] = [
                PyzIntegraLogTableMap::COL_CREATED_AT => $this->formatDateTime($logEntry->getCreatedAt()),
                PyzIntegraLogTableMap::COL_LEVEL => $this->formatLevel($logEntry->getLevel()),
                PyzIntegraLogTableMap::COL_FK_BRANCH => $logEntry->getVirtualColumn(self::COL_BRANCH),
                PyzIntegraLogTableMap::COL_MESSAGE => $logEntry->getMessage(),
            ];
        }

        return $results;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    protected function formatDateTime(DateTime $dateTime): string
    {
        $dateTime
            ->setTimezone(new DateTimeZone($this->integraConfig->getTimezone()));

        return $dateTime->format(static::TIME_FORMAT);
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function formatLevel($value): string
    {
        switch ($value) {
            case PyzIntegraLogTableMap::COL_LEVEL_INFO:
                return $this->generateViewButton(
                    '#',
                    'INFO'
                );
            case PyzIntegraLogTableMap::COL_LEVEL_WARNING:
                return $this->generateEditButton(
                    '#',
                    'WARNING'
                );
            case PyzIntegraLogTableMap::COL_LEVEL_ERROR:
                return $this->generateRemoveButton(
                    '#',
                    'ERROR'
                );
        }
    }
}
