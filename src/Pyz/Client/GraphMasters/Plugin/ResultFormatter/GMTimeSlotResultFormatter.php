<?php
/**
 * Durst - project - GMTimeSlotResultFormatter.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 04.11.21
 * Time: 14:05
 */

namespace Pyz\Client\GraphMasters\Plugin\ResultFormatter;


use DateTime;
use DateTimeZone;
use Elastica\ResultSet;
use Exception;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

class GMTimeSlotResultFormatter extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'gm_time_slot_result_formatter';

    /**
     * @param ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return mixed
     */
    protected function formatSearchResult(ResultSet $searchResult, array $requestParameters)
    {
        $values = [];

        foreach ($searchResult as $item) {
           $values[] = [
                   'start_time' => $this->getDateTimeWTimezone($item->getSource()['start_time']),
                    'end_time' => $this->getDateTimeWTimezone($item->getSource()['end_time']),
               ];
        }

        return $values;
    }

    /**
     * @param string $timestamp
     * @return string
     * @throws Exception
     */
    protected function getDateTimeWTimezone(string $timestamp) : string
    {
        $date = (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone('Europe/Berlin'));

        return $date->format('c');
    }

    /**
     * @return string
     * @api
     *
     */
    public function getName()
    {
        return static::NAME;
    }

}
