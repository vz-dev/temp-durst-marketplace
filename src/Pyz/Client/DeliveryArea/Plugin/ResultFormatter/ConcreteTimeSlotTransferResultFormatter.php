<?php
/**
 * Durst - project - ConcreteTimeSlotTransferResultFormatter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.18
 * Time: 10:10
 */

namespace Pyz\Client\DeliveryArea\Plugin\ResultFormatter;

use Elastica\Result;
use Elastica\ResultSet;
use Generated\Shared\Search\TimeSlotIndexMap;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

class ConcreteTimeSlotTransferResultFormatter extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'concrete_time_slot_transfer_result_formatter';

    /**
     * @param \Elastica\ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return mixed
     */
    protected function formatSearchResult(
        ResultSet $searchResult,
        array $requestParameters
    ) {
        $transfers = [];
        foreach ($searchResult as $result) {
            $transfers[] = $this->resultToTransfer($result);
        }

        return $transfers;
    }

    /**
     * @param \Elastica\Result $result
     *
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    protected function resultToTransfer(Result $result): ConcreteTimeSlotTransfer
    {
        $source = $result->getSource();

        return (new ConcreteTimeSlotTransfer())
            ->setMinValue($source[TimeSlotIndexMap::MIN_VALUE_FIRST])
            ->setMinValueFirst($source[TimeSlotIndexMap::MIN_VALUE_FIRST])
            ->setMinValueFollowing($source[TimeSlotIndexMap::MIN_VALUE_FOLLOWING])
            ->setIdBranch($source[TimeSlotIndexMap::ID_BRANCH])
            ->setIdConcreteTimeSlot($source[TimeSlotIndexMap::ID_TIME_SLOT])
            ->setFormattedString($source[TimeSlotIndexMap::TIME_SLOT_STRING])
            ->setMinUnits($source[TimeSlotIndexMap::MIN_UNITS])
            ->setStartTime($source[TimeSlotIndexMap::TIME_SLOT_START_DATE])
            ->setEndTime($source[TimeSlotIndexMap::TIME_SLOT_END_DATE])
            ->setStartTimeRaw($source[TimeSlotIndexMap::TIME_SLOT_START_DATE_RAW])
            ->setEndTimeRaw($source[TimeSlotIndexMap::TIME_SLOT_END_DATE_RAW])
            ->setFkTimeSlot($source[TimeSlotIndexMap::FK_TIME_SLOT])
            ->setDeliveryCosts($source[TimeSlotIndexMap::DELIVERY_COSTS])
            ->setMaxCustomer($source[TimeSlotIndexMap::MAX_CUSTOMERS])
            ->setRemainingCustomer($source[TimeSlotIndexMap::REMAINING_CUSTOMERS])
            ->setRemainingPayload($source[TimeSlotIndexMap::REMAINING_PAYLOAD])
            ->setRemainingProduct($source[TimeSlotIndexMap::REMAINING_PRODUCTS]);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
