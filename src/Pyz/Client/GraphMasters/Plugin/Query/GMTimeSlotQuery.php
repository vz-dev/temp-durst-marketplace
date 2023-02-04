<?php
/**
 * Durst - project - GMTimeSlotQuery.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.10.21
 * Time: 20:40
 */

namespace Pyz\Client\GraphMasters\Plugin\Query;


use DateInterval;
use DateTime;
use DateTimeZone;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use Generated\Shared\Search\GmTimeSlotIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class GMTimeSlotQuery extends AbstractPlugin implements QueryInterface
{
    /**
     * /**
     * @var Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $gmSettings;

    /**
     * @var
     */
    protected $dayLimit;

    /**
     * GMTimeSlotQuery constructor.
     * @param array $gmSettings
     * @param int $dayLimit
     */
    public function __construct(array $gmSettings, int $dayLimit)
    {
        $this->limit = 1000;
        $this->dayLimit = $dayLimit;
        $this->gmSettings = $gmSettings;
        $this->query = $this->createSearchQuery();
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
     * @return Query
     */
    protected function createSearchQuery(): Query
    {
        $boolQuery = (new BoolQuery());

        foreach ($this->addOpeningTimeQuery() as $item) {
            $boolQuery->addShould($item);
        }

        //$boolQuery->addFilter(new Range(
        //    GMTimeSlotIndexMap::START_TIME,
        //    [
        //       'gte' => "now",
        //        'lte'=> $this->getUpperDayLimit(),
        //    ]
        //));

        //$earliestStart = $this->getEarliestOpeningAfterCommisioning();

        // todo add absences
        $boolQuery->addMustNot(
            (new BoolQuery())
                ->addShould(
                    new Range(
                        GmTimeSlotIndexMap::START_TIME,
                        [
                            'lte' => $this->getNowTimeFormatted()
                        ]
                    )
                )
                ->addShould(
                    new Range(
                        GmTimeSlotIndexMap::START_TIME,
                        [
                            'gte' => $this->getNowTimeFormatted(),
                            'lt' => $this->getEarliestOpeningAfterCommisioning()
                        ]
                    )
                )
        );


        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSort(
                [
                    GMTimeSlotIndexMap::START_TIME => [
                        'order' => 'asc',
                    ],
                ]
            )
            ->setSize($this->limit !== null ? $this->limit : 10000);


        $queryString =  json_encode(array('query' => $query->getQuery()->toArray()));
        //die();

        return $query;
    }

    /**
     * @return string
     */
    protected function getNowTimeFormatted(): string
    {
        $now = (new DateTime());
        $now->setTimezone(new DateTimeZone('Europe/Berlin'));

        return $now->format('U');
    }

    /**
     * @return DateTime
     */
    protected function getBufferedDateTime(): DateTime
    {
        $bufferTime = new DateInterval(DeliveryAreaConfig::CONCRETE_TIME_SLOT_QUERY_BUFFER);
        $now = (new DateTime('now/d'));
        $now->setTimezone($this->timeZone);
        $now->add($bufferTime);
        return $now;
    }

    protected function addOpeningTimeQuery() : array
    {
        $boolQueries = [];

        foreach ($this->gmSettings['opening_times'] as $weekday => $dayItems)
        {
            foreach ($dayItems as $item)
            {
                $boolQuery = (new BoolQuery());
                $boolQuery
                    ->addMust(new Range(
                        GMTimeSlotIndexMap::START_HOUR_MIN,
                        [
                            'gte' => $item['start_time'],
                            'format' => 'hour_minute'
                        ]
                    ))->addMust(new Range(
                        GMTimeSlotIndexMap::END_HOUR_MIN,
                        [
                            'lte' => $item['end_time'],
                            'format' => 'hour_minute'
                        ]
                    ))
                    ->addMust(new Match('day_of_week', $this->getNumericDayOfWeek($weekday)))
                    ->addMust(new Range('date', [
                        'gte' => "now/d",
                        'lte'=> $this->getUpperDayLimit(),
                    ]));

                $boolQueries[] = $boolQuery;
            }
        }

        return $boolQueries;
    }

    /**
     * @param string $weekDay
     * @return string
     */
    protected function getNumericDayOfWeek(string $weekDay): string
    {
        $days = ['sunday' => 0, 'monday' => 1,'tuesday' => 2,'wednesday' => 3,'thursday' => 4,'friday' => 5,'saturday' => 6];

        return (string) $days[$weekDay];
    }

    /**
     * @return string
     */
    protected function getUpperDayLimit() : string
    {
        return sprintf('now+%dd/d', $this->dayLimit);
    }

    /**
     * @return int
     */
    protected function getEarliestOpeningAfterCommisioning() : int
    {
        $now = (new DateTime())->setTimezone(new DateTimeZone('Europe/Berlin'));
        $day = $now->format('w');

        $nextCommissioning = null;
        $diffTime = 86400 * $this->dayLimit;

        foreach ($this->gmSettings['commissioning_times'] as $dayOfWeek => $commissioning_time_for_day)
        {
            foreach ($commissioning_time_for_day as $commission_slot)
            {
                list($hours, $minutes) = explode(':', $commission_slot['start_time']);
                $commisionSlotDateTime = (clone $now)->setTime($hours, $minutes);
                if($this->getNumericDayOfWeek($dayOfWeek) !== $day)
                {
                    $commisionSlotDateTime->modify(sprintf('next %s', $dayOfWeek));
                }


                    $currentDiff = $commisionSlotDateTime->format('U') - $now->format('U');


                if(($currentDiff < $diffTime && $currentDiff > 0))
                {
                    $diffTime = $currentDiff;
                    list($hours, $minutes) = explode(':', $commission_slot['end_time']);
                    $nextCommissioning = $commisionSlotDateTime->setTime($hours, $minutes);
                }
            }
        }

        $nextOpening= null;
        $diffTime = 86400 * $this->dayLimit;

        $day = $nextCommissioning->format('w');

        foreach ($this->gmSettings['opening_times'] as $dayOfWeek => $opening_time_for_day)
        {
            foreach ($opening_time_for_day as $opening_slot)
            {
                list($hours, $minutes) = explode(':', $opening_slot['start_time']);
                $openingSlotDateTime = (clone $nextCommissioning)->setTime($hours, $minutes);
                if($this->getNumericDayOfWeek($dayOfWeek) !== $day)
                {
                    $openingSlotDateTime->modify(sprintf('next %s', $dayOfWeek));
                }

                $currentDiff = $openingSlotDateTime->format('U') - $nextCommissioning->format('U');

                if(($currentDiff < $diffTime && $currentDiff > 0))
                {
                    $diffTime = $currentDiff;
                    $nextOpening = $openingSlotDateTime;
                }
            }
        }

        return $nextOpening->getTimestamp();
    }
}
