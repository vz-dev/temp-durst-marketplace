<?php
/**
 * Durst - project - MatchBranchIdsQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.11.18
 * Time: 11:22
 */

namespace Pyz\Client\Merchant\Plugin\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Generated\Shared\Search\BranchIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class MatchBranchIdsQuery extends AbstractPlugin implements QueryInterface
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
     * MatchBranchIdsQuery constructor.
     *
     * @param array $branchIds
     * @param int $limit
     */
    public function __construct(array $branchIds, int $limit)
    {
        $this->limit = $limit;
        $this->query = $this->createSearchQuery($branchIds);
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
     * @param int[] $branchIds
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(array $branchIds)
    {
        $boolQuery = (new BoolQuery())
            ->addFilter(new Terms(BranchIndexMap::ID_BRANCH, $branchIds));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSize($this->limit);

        return $query;
    }
}
