<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:41
 */

namespace Pyz\Zed\DeliveryArea\Business\Map;


use DateTime;
use DateTimeZone;
use Generated\Shared\Search\TimeSlotIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Finder\TimeSlotFinder;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class TimeslotDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $timeslotData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     * @throws \Exception
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $timeslotData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(DeliveryAreaConstants::TIMESLOT_SEARCH_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::ID_BRANCH, $timeslotData[TimeSlotIndexMap::ID_BRANCH])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::FK_TIME_SLOT, $timeslotData[TimeSlotIndexMap::FK_TIME_SLOT])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::DELIVERY_COSTS, $timeslotData[TimeSlotIndexMap::DELIVERY_COSTS])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::TIME_SLOT_START_DATE, $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_START_TIME])->format(DeliveryAreaConstants::TIMESLOT_DATE_FORMAT_SEARCH))
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::ID_TIME_SLOT, $timeslotData[TimeSlotIndexMap::ID_TIME_SLOT])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::MAX_CUSTOMERS, $timeslotData[TimeSlotIndexMap::MAX_CUSTOMERS])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::MAX_PRODUCTS, $timeslotData[TimeSlotIndexMap::MAX_PRODUCTS])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::MIN_UNITS, $timeslotData[TimeSlotIndexMap::MIN_UNITS])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::MIN_VALUE_FIRST, $timeslotData[TimeSlotIndexMap::MIN_VALUE_FIRST])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::MIN_VALUE_FOLLOWING, $timeslotData[TimeSlotIndexMap::MIN_VALUE_FOLLOWING])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::PREP_TIME, $timeslotData[TimeSlotIndexMap::PREP_TIME])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::TIME_SLOT_STRING, $this->getTimeslotString($timeslotData))
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::TIME_SLOT_END_DATE, $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_END_TIME])->format(DeliveryAreaConstants::TIMESLOT_DATE_FORMAT_SEARCH))
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::TIME_SLOT_START_DATE_RAW, $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_START_TIME])->format(DeliveryAreaConstants::ELASTICSEARCH_TIMESTAMP_FORMAT))
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::TIME_SLOT_END_DATE_RAW, $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_END_TIME])->format(DeliveryAreaConstants::ELASTICSEARCH_TIMESTAMP_FORMAT))
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::REMAINING_PRODUCTS, $timeslotData[TimeSlotIndexMap::REMAINING_PRODUCTS])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::REMAINING_PAYLOAD, $timeslotData[TimeSlotIndexMap::REMAINING_PAYLOAD])
            ->addSearchResultData($pageMapTransfer, TimeSlotIndexMap::REMAINING_CUSTOMERS, $timeslotData[TimeSlotIndexMap::REMAINING_CUSTOMERS]);

        return $pageMapTransfer;
    }

    /**
     * @param string $dateString
     * @return \DateTime
     * @throws \Exception
     */
    protected function getDateWithTimezone(string $dateString) : DateTime
    {
        $config = new DeliveryAreaConfig();
        $timezone = new DateTimeZone($config->getProjectTimeZone());

        $date = new DateTime($dateString);
        $date->setTimezone($timezone);

        return $date;
    }

    /**
     * @param array $timeslotData
     * @return string
     * @throws \Exception
     */
    protected function getTimeslotString(array $timeslotData) : string
    {
        $startTime = $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_START_TIME]);
        $endTime = $this->getDateWithTimezone($timeslotData[DeliveryAreaConstants::TIMESLOT_KEY_END_TIME]);

        $startString = $startTime->format(TimeSlotFinder::FORMATTED_STRING_FORMAT);
        $endString = $endTime->format(TimeSlotFinder::FORMATTED_STRING_FORMAT_TIME);

        $formattedString = sprintf(
            TimeSlotFinder::FORMATTED_STRING_TEMPLATE,
            $startString,
            $endString
        );

        foreach (TimeSlotFinder::GERMAN_DAYS_MAP as $key => $day) {
            $formattedString = str_replace($key, $day, $formattedString);
        }

        return $formattedString;
    }
}
