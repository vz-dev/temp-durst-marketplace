<?php
/**
 * Durst - project - TimeSlotMatchZipCodeAndBranchIdsQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.18
 * Time: 10:04
 */

namespace Pyz\Client\DeliveryArea\Plugin\Query;

use DateInterval;
use DateTime;
use DateTimeZone;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use Elastica\Query\Terms;
use Generated\Shared\Search\TimeSlotIndexMap;
use Pyz\Client\DeliveryArea\DeliveryAreaConfig;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class TimeSlotMatchZipCodeAndBranchIdsQuery extends AbstractPlugin implements QueryInterface
{
    /**
     * @var \Elastica\Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $amountProducts;

    /**
     * @var int|null
     */
    protected $amountProductWeight;

    /**
     * @var \DateTimeZone
     */
    protected $timeZone;

    /**
     * @var bool
     */
    protected $fetchFullyBookedTimeSlots;

    /**
     * TimeSlotMatchZipCodeAndBranchIdsQuery constructor.
     * @param string $zipCode
     * @param array $branchIds
     * @param int|null $limit
     * @param int $amountProducts
     * @param int|null $amountProductWeight
     * @param DateTimeZone $timeZone
     * @param bool $fetchFullyBookedTimeSlots
     */
    public function __construct(
        string $zipCode,
        array $branchIds,
        ?int $limit,
        int $amountProducts,
        ?int $amountProductWeight,
        DateTimeZone $timeZone,
        bool $fetchFullyBookedTimeSlots = false
    ) {
        $this->limit = $limit;
        $this->amountProducts = $amountProducts;
        $this->amountProductWeight = $amountProductWeight;
        $this->timeZone = $timeZone;
        $this->fetchFullyBookedTimeSlots = $fetchFullyBookedTimeSlots;
        $this->query = $this->createSearchQuery($zipCode, $branchIds);
    }

    /**
     * @api
     *
     * @return mixed A query object.
     */
    public function getSearchQuery()
    {
        return $this->query;
    }

    /**
     * @param string $zipCode
     * @param array $branchIds
     *
     * @return Query
     */
    protected function createSearchQuery(string $zipCode, array $branchIds): Query
    {
        $boolQuery = (new BoolQuery())
            ->addMust(new Match(TimeSlotIndexMap::ZIP_CODE, $zipCode));

        if ($this->fetchFullyBookedTimeSlots !== true) {
            $boolQuery->addFilter($this->addPayloadFilterQuery());
        }

        $boolQuery
            ->addFilter(new Terms(TimeSlotIndexMap::ID_BRANCH, $branchIds))
            ->addFilter(new Range(
                TimeSlotIndexMap::TIME_SLOT_START_DATE,
                [
                    'gte' => $this->getNowPlusBufferTimeFormatted(),
                ]
            ));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSort(
                [
                    TimeSlotIndexMap::TIME_SLOT_START_DATE => [
                        'order' => 'asc',
                    ],
                ]
            )
            ->setSize($this->limit !== null ? $this->limit : 10000);

        return $query;
    }

    /**
     * @return string
     */
    protected function getNowPlusBufferTimeFormatted(): string
    {
        return $this
            ->getBufferedDateTime()
            ->format(DeliveryAreaConstants::ELASTICSEARCH_DATE_TIME_FORMAT);
    }

    /**
     * @return BoolQuery
     */
    protected function addPayloadFilterQuery(): BoolQuery
    {
        return (new BoolQuery())
                ->addShould(
                    (new BoolQuery())
                        ->addMustNot(
                            new Query\Exists("remaining_payload")
                        )
                )
                ->addShould(
                    new Range(
                        TimeSlotIndexMap::REMAINING_PAYLOAD,
                        [
                            'gte' => $this->amountProductWeight,
                        ]
                    )
                );
    }

    /**
     * @return \DateTime
     */
    protected function getBufferedDateTime(): DateTime
    {
        $bufferTime = new DateInterval(DeliveryAreaConfig::CONCRETE_TIME_SLOT_QUERY_BUFFER);
        $now = (new DateTime('now'));
        $now->setTimezone($this->timeZone);
        $now->add($bufferTime);
        return $now;
    }
}
