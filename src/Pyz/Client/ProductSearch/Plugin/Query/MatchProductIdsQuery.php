<?php
/**
 * Durst - project - MatchProductIdsQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.11.18
 * Time: 10:00
 */

namespace Pyz\Client\ProductSearch\Plugin\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Generated\Shared\Search\ProductIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class MatchProductIdsQuery extends AbstractPlugin implements QueryInterface
{
    /**
    /**
     * @var \Elastica\Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $limit;

    /**
     * MatchProductIdsQuery constructor.
     *
     * @param array $productIds
     * @param int $limit
     */
    public function __construct(array $productIds, int $limit)
    {
        $this->limit = $limit;
        $this->query = $this->createSearchQuery($productIds);
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
     * @param int[] $productIds
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(array $productIds)
    {
        $boolQuery = (new BoolQuery())
            ->addFilter(new Terms(ProductIndexMap::ID_PRODUCT, $productIds));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSize($this->limit);

        return $query;
    }
}
