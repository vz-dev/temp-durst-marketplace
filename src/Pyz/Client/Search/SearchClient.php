<?php
/**
 * Durst - project - SearchClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.11.18
 * Time: 18:20
 */

namespace Pyz\Client\Search;

use Spryker\Client\Search\Dependency\Plugin\QueryInterface;
use Spryker\Client\Search\SearchClient as SprykerSearchClient;

/**
 * Class SearchClient
 * @package Pyz\Client\Search
 * @method \Pyz\Client\Search\SearchFactory getFactory()
 */
class SearchClient extends SprykerSearchClient implements SearchClientInterface
{

    /**
     * Specification:
     * - Run in the time slot index
     * - Runs the search query based on the search configuration provided by this client
     * - If there's no result formatter given then the raw search result will be returned
     * - The formatted search result will be an associative array where the keys are the name and the values are the formatted results
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array
     */
    public function searchTimeSlotIndex(
        QueryInterface $searchQuery,
        array $resultFormatters = [],
        array $requestParameters = []
    ): array
    {
        return $this
            ->getFactory()
            ->createElasticsearchTimeSlotSearchHandler()
            ->search($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * Specification:
     * - Run in the graphmasters time slot index
     * - Runs the search query based on the search configuration provided by this client
     * - If there's no result formatter given then the raw search result will be returned
     * - The formatted search result will be an associative array where the keys are the name and the values are the formatted results
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     */
    public function searchGMTimeSlotIndex(
        QueryInterface $searchQuery,
        array $resultFormatters = [],
        array $requestParameters = []
    )
    {
        return $this
            ->getFactory()
            ->createElasticsearchGMTimeSlotSearchHandler()
            ->search($searchQuery, $resultFormatters, $requestParameters);
    }
}
