<?php
/**
 * Durst - project - MatchBranchIdQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.11.18
 * Time: 11:52
 */

namespace Pyz\Client\Merchant\Plugin\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Type;
use Generated\Shared\Search\BranchIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class MatchBranchIdQuery extends AbstractPlugin implements QueryInterface
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
     * MatchBranchIdQuery constructor.
     *
     * @param int $branchId
     */
    public function __construct(int $branchId)
    {
        $this->limit = 1;
        $this->query = $this->createSearchQuery($branchId);
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
     * @param int $branchId
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(int $branchId)
    {
        $boolQuery = (new BoolQuery())
            ->addMust(new Match(BranchIndexMap::ID_BRANCH, $branchId))
            ->addMust(new Type('branch'));

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSize($this->limit);

        return $query;
    }
}
