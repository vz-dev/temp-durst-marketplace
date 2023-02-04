<?php
/**
 * Durst - project - MatchZipCodeQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.18
 * Time: 14:50
 */

namespace Pyz\Client\DeliveryArea\Plugin\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Generated\Shared\Search\DeliveryAreaIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class MatchZipCodeQuery extends AbstractPlugin implements QueryInterface
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
     * MatchZipCodeQuery constructor.
     *
     * @param string $zipCode
     * @param int $limit
     */
    public function __construct(string $zipCode, int $limit)
    {
        $this->limit = $limit;
        $this->query = $this->createSearchQuery($zipCode);
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
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(string $zipCode)
    {
        $boolQuery = (new BoolQuery())
            ->addMust(new Match(DeliveryAreaIndexMap::ZIP_CODE, $zipCode));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSize($this->limit);

        return $query;
    }
}
