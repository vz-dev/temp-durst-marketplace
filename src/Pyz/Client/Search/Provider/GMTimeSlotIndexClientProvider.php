<?php
/**
 * Durst - project - GMTimeSlotIndexClientProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.10.21
 * Time: 19:42
 */

namespace Pyz\Client\Search\Provider;


use Elastica\Index;
use Pyz\Shared\Search\SearchConstants;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Search\Provider\AbstractSearchClientProvider;

class GMTimeSlotIndexClientProvider extends AbstractSearchClientProvider
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
            $index = Config::get(SearchConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME);
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
