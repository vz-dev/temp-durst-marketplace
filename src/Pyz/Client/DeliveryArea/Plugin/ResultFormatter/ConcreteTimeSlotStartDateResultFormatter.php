<?php

namespace Pyz\Client\DeliveryArea\Plugin\ResultFormatter;

use Elastica\ResultSet;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

class ConcreteTimeSlotStartDateResultFormatter extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'concrete_time_slot_start_date_result_formatter';

    /**
     * @param ResultSet $searchResultSet
     * @param array $requestParameters
     *
     * @return mixed
     */
    protected function formatSearchResult(ResultSet $searchResultSet, array $requestParameters)
    {
        $values = [];

        foreach ($searchResultSet as $searchResult) {
            $values[] = $searchResult->getSource()['time_slot_start_date_raw'];
        }

        return $values;
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
