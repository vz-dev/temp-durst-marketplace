<?php
/**
 * Durst - project - GMTimeSlotCollector.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.10.21
 * Time: 07:53
 */

namespace Pyz\Zed\Collector\Business\Search;


use DateTime;
use DateTimeZone;
use Generated\Shared\Search\GmTimeSlotIndexMap;
use Pyz\Zed\Collector\CollectorConfig;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\GMTimeSlotCollectorQuery;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Search\AbstractSearchPdoCollector;

class GMTimeSlotCollector extends AbstractSearchPdoCollector
{
    /**
     * @var \Pyz\Zed\Collector\CollectorConfig
     */
    protected $config;

    public const ELASTICSEARCH_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    public const ELASTICSEARCH_DATE_FORMAT = 'Y-m-d';
    public const ELASTICSEARCH_DAY_FORMAT = 'w';
    public const ELASTICSEARCH_TIMESTAMP_FORMAT = 'U';
    public const ELASTICSEARCH_HOUR_MIN_FORMAT = 'H:i';

    /**
     * GMTimeSlotCollector constructor.
     * @param UtilDataReaderServiceInterface $utilDataReaderService
     * @param CollectorConfig $config
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        CollectorConfig $config
    ) {
        parent::__construct($utilDataReaderService);

        $this->config = $config;
    }

    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Exception
     */
    protected function collectItem($touchKey, array $collectItemData): array
    {
        dump($collectItemData);
        return [
            GmTimeSlotIndexMap::ID_TIME_SLOT => $collectItemData[GMTimeSlotCollectorQuery::ID_GM_TIME_SLOT],
            GmTimeSlotIndexMap::START_TIME => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::START_TIME]),
            GmTimeSlotIndexMap::END_TIME => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::END_TIME]),
            GmTimeSlotIndexMap::START_HOUR_MIN => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::START_TIME], self::ELASTICSEARCH_HOUR_MIN_FORMAT),
            GmTimeSlotIndexMap::END_HOUR_MIN => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::END_TIME], self::ELASTICSEARCH_HOUR_MIN_FORMAT),
            GmTimeSlotIndexMap::DATE => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::END_TIME], self::ELASTICSEARCH_DATE_FORMAT),
            GmTimeSlotIndexMap::DAY_OF_WEEK => $this->getDateWithTimezone($collectItemData[GMTimeSlotCollectorQuery::END_TIME], self::ELASTICSEARCH_DAY_FORMAT),
        ];
    }

    /**
     * @param string $dateString
     * @param string|null $format
     * @return string
     * @throws \Exception
     */
    protected function getDateWithTimezone(string $dateString, ?string $format = self::ELASTICSEARCH_TIMESTAMP_FORMAT): string
    {
        $timezone = new DateTimeZone($this->config->getProjectTimeZone());
        $date = new DateTime($dateString);
        $date
            ->setTimezone($timezone);

        return $date
            ->format($format);
    }
    /**
     * @return string
     */
    protected function collectResourceType(): string
    {
        return 'gm_time_slot';
    }
}
