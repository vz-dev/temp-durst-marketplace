<?php

namespace Pyz\Client\DeliveryArea\Plugin\Query;

use DateTime;
use DateTimeZone;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Range;
use Elastica\Query\Terms;
use Generated\Shared\Search\TimeSlotIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class TimeSlotFutureDatesMatchBranchIdsQuery extends AbstractPlugin implements QueryInterface
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var DateTimeZone
     */
    protected $timeZone;

    /**
     * @param array $branchIds
     * @param DateTimeZone $timeZone
     */
    public function __construct(array $branchIds, DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
        $this->query = $this->createSearchQuery($branchIds);
    }

    /**
     * @api
     *
     * @return mixed
     */
    public function getSearchQuery()
    {
        return $this->query;
    }

    /**
     * @param array $branchIds
     *
     * @return Query
     */
    protected function createSearchQuery(array $branchIds)
    {
        $boolQuery = (new BoolQuery())
            ->addFilter(new Terms(TimeSlotIndexMap::ID_BRANCH, $branchIds))
            ->addFilter(new Range(
                TimeSlotIndexMap::TIME_SLOT_START_DATE_RAW,
                [
                    'gte' => $now = (new DateTime('tomorrow midnight'))
                        ->setTimezone($this->timeZone)
                        ->format('U')
                ]
            ));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSort(
                [
                    TimeSlotIndexMap::TIME_SLOT_START_DATE_RAW => [
                        'order' => 'asc',
                    ],
                ]
            )
            ->setSource(['time_slot_start_date_raw'])
            ->setSize(10000);

        return $query;
    }
}
