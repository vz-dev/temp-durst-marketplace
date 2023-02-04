<?php
/**
 * Durst - project - SearchFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 18:34
 */

namespace Pyz\Client\Search;

use Pyz\Client\Search\Provider\GMTimeSlotIndexClientProvider;
use Pyz\Client\Search\Provider\TimeSlotIndexClientProvider;
use Spryker\Client\Search\Model\Handler\ElasticsearchSearchHandler;
use Spryker\Client\Search\SearchFactory as SprykerSearchFactory;

class SearchFactory extends SprykerSearchFactory
{
    /**
     * @return \Spryker\Client\Search\Model\Handler\SearchHandlerInterface
     */
    public function createElasticsearchTimeSlotSearchHandler(): ElasticsearchSearchHandler
    {
        return new ElasticsearchSearchHandler(
            $this->createTimeSlotIndexClientProvider()->getClient()
        );
    }

    /**
     * @return \Pyz\Client\Search\Provider\TimeSlotIndexClientProvider
     */
    protected function createTimeSlotIndexClientProvider(): TimeSlotIndexClientProvider
    {
        return new TimeSlotIndexClientProvider();
    }

    /**
     * @return \Spryker\Client\Search\Model\Handler\SearchHandlerInterface
     */
    public function createElasticsearchGMTimeSlotSearchHandler(): ElasticsearchSearchHandler
    {
        return new ElasticsearchSearchHandler(
            $this->createGMTimeSlotIndexClientProvider()->getClient()
        );
    }

    /**
     * @return \Pyz\Client\Search\Provider\GMTimeSlotIndexClientProvider
     */
    protected function createGMTimeSlotIndexClientProvider(): GMTimeSlotIndexClientProvider
    {
        return new GMTimeSlotIndexClientProvider();
    }
}
