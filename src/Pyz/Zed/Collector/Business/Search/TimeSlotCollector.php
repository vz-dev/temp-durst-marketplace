<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 12.11.18
 * Time: 12:33
 */

namespace Pyz\Zed\Collector\Business\Search;

use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Search\TimeSlotIndexMap;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\Collector\CollectorConfig;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\TimeSlotCollectorQuery;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Search\AbstractSearchPdoCollector;

class TimeSlotCollector extends AbstractSearchPdoCollector
{
    /**
     * @var \Pyz\Zed\Collector\CollectorConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $deliveryAreaQueryContainer;

    /**
     * @var DepositFacadeInterface
     */
    protected $depositFacade;

    /**
     * TimeSlotCollector constructor.
     * @param UtilDataReaderServiceInterface $utilDataReaderService
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     * @param DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
     * @param DepositFacadeInterface $depositFacade
     * @param CollectorConfig $config
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        DeliveryAreaFacadeInterface $deliveryAreaFacade,
        DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer,
        DepositFacadeInterface $depositFacade,
        CollectorConfig $config
    ) {
        parent::__construct($utilDataReaderService);

        $this->deliveryAreaFacade = $deliveryAreaFacade;
        $this->deliveryAreaQueryContainer = $deliveryAreaQueryContainer;
        $this->depositFacade = $depositFacade;
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
        return [
            TimeSlotIndexMap::ID_TIME_SLOT => $collectItemData[TimeSlotCollectorQuery::ID_TIME_SLOT],
            TimeSlotIndexMap::FK_TIME_SLOT => $collectItemData[TimeslotCollectorQuery::FK_TIME_SLOT],
            TimeSlotIndexMap::TIME_SLOT_START_DATE =>
                $this->getDateWithTimezoneAndPrepTime(
                    $collectItemData[TimeslotCollectorQuery::START],
                    $collectItemData[TimeslotCollectorQuery::PREP_TIME]
                ),
            TimeSlotIndexMap::TIME_SLOT_START_DATE_RAW =>
                $this->getDateWithTimezone(
                    $collectItemData[TimeslotCollectorQuery::START]
                ),
            TimeSlotIndexMap::TIME_SLOT_END_DATE =>
                $this->getDateWithTimezoneAndPrepTime(
                    $collectItemData[TimeslotCollectorQuery::END],
                    $collectItemData[TimeslotCollectorQuery::PREP_TIME]
                ),
            TimeSlotIndexMap::TIME_SLOT_END_DATE_RAW =>
                $this->getDateWithTimezone(
                    $collectItemData[TimeslotCollectorQuery::END]
                ),
            TimeSlotIndexMap::ID_BRANCH => $collectItemData[TimeslotCollectorQuery::ID_BRANCH],
            TimeSlotIndexMap::MIN_UNITS => $collectItemData[TimeslotCollectorQuery::MIN_UNITS],
            TimeSlotIndexMap::MIN_VALUE_FIRST => $collectItemData[TimeslotCollectorQuery::MIN_VALUE_FIRST],
            TimeSlotIndexMap::MIN_VALUE_FOLLOWING => $collectItemData[TimeslotCollectorQuery::MIN_VALUE_FOLLOWING],
            TimeSlotIndexMap::MAX_CUSTOMERS => $collectItemData[TimeslotCollectorQuery::MAX_CUSTOMERS],
            TimeSlotIndexMap::MAX_PRODUCTS => $collectItemData[TimeslotCollectorQuery::MAX_PRODUCTS],
            TimeSlotIndexMap::PREP_TIME => $collectItemData[TimeslotCollectorQuery::PREP_TIME],
            TimeSlotIndexMap::DELIVERY_COSTS => $collectItemData[TimeslotCollectorQuery::DELIVERY_COST],
            TimeSlotIndexMap::ZIP_CODE => $collectItemData[TimeslotCollectorQuery::ZIP_CODE],
            TimeSlotIndexMap::TIME_SLOT_STRING =>
                $this->createTimeSlotString(
                    $collectItemData[TimeslotCollectorQuery::START],
                    $collectItemData[TimeslotCollectorQuery::END]
                ),
            TimeSlotIndexMap::REMAINING_PRODUCTS => $this->getCurrentRemainingProductCount($collectItemData),
            TimeSlotIndexMap::REMAINING_PAYLOAD => $this->getRemainingWeight($collectItemData),
            TimeSlotIndexMap::REMAINING_CUSTOMERS => $this->getRemainingCustomers($collectItemData)
        ];
    }

    /**
     * @param string $start
     * @param string $end
     * @return string
     * @throws \Exception
     */
    protected function createTimeSlotString(
        string $start,
        string $end
    ) : string
    {
        $startDateTime = new DateTime($start);
        $endDateTime = new DateTime($end);

        return $this
            ->deliveryAreaFacade
            ->createFormattedTimeSlotString($startDateTime, $endDateTime);
    }

    /**
     * @param string $dateString
     * @param int $prepTime
     * @return string
     * @throws \Exception
     */
    protected function getDateWithTimezoneAndPrepTime(string $dateString, int $prepTime): string
    {
        $timezone = new DateTimeZone($this->config->getProjectTimeZone());
        $prepTimeInterval = new DateInterval('PT' . $prepTime . 'M');

        $date = new DateTime($dateString);
        $date->setTimezone($timezone);
        $date->sub($prepTimeInterval);

        return $date->format(DeliveryAreaConstants::ELASTICSEARCH_DATE_TIME_FORMAT);
    }

    /**
     * @param string $dateString
     * @return string
     * @throws \Exception
     */
    protected function getDateWithTimezone(string $dateString): string
    {
        $timezone = new DateTimeZone($this->config->getProjectTimeZone());
        $date = new DateTime($dateString);
        $date
            ->setTimezone($timezone);

        return $date
            ->format(DeliveryAreaConstants::ELASTICSEARCH_TIMESTAMP_FORMAT);
    }

    /**
     * @param array $collectItemData
     * @return int
     */
    protected function getCurrentRemainingProductCount(array $collectItemData): int
    {
        return max(
            0,
            ($collectItemData[TimeSlotCollectorQuery::MAX_PRODUCTS] - $collectItemData[TimeSlotCollectorQuery::VIRTUAL_PRODUCTS])
        );
    }

    /**
     * @param array $collectItemData
     * @return int
     */
    protected function getRemainingWeight(array $collectItemData): int
    {
        $allowedWeight = $collectItemData[TimeSlotCollectorQuery::ALLOWED_WEIGHT] * 1000;
        $currentWeight = $collectItemData[TimeSlotCollectorQuery::VIRTUAL_WEIGHT];

        return max(
            0,
            ($allowedWeight - $currentWeight)
        );
    }

    /**
     * @param array $collectItemData
     * @return int
     */
    protected function getRemainingCustomers(array $collectItemData): int
    {
        return max(
            0,
            ($collectItemData[TimeSlotCollectorQuery::MAX_CUSTOMERS] - $collectItemData[TimeSlotCollectorQuery::VIRTUAL_CUSTOMERS])
        );
    }

    /**
     * @return string
     */
    protected function collectResourceType(): string
    {
        return 'time_slot';
    }
}
