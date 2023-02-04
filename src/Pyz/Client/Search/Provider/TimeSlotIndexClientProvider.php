<?php
/**
 * Durst - project - TimeSlotIndexClientProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 18:24
 */

namespace Pyz\Client\Search\Provider;

use Elastica\Index;
use Pyz\Shared\Search\SearchConstants;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Search\Provider\AbstractSearchClientProvider;

class TimeSlotIndexClientProvider extends AbstractSearchClientProvider
{
    /**
     * @param null|string $index
     *
     * @return \Elastica\Index
     */
    protected function createZedClient(?string $index = null): Index
    {
        $client = parent::createZedClient();

        if ($index === null) {
            $index = Config::get(SearchConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME);
        }

        return $client->getIndex($index);
    }

    /**
     * @param null|string $index
     *
     * @return \Elastica\Index
     */
    public function getClient(?string $index = null): Index
    {
        return $this->createZedClient($index);
    }
}
