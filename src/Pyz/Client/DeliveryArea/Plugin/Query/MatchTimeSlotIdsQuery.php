<?php
/**
 * Durst - project - MatchTimeSlotIdsQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.11.18
 * Time: 12:55
 */

namespace Pyz\Client\DeliveryArea\Plugin\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Generated\Shared\Search\TimeSlotIndexMap;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

class MatchTimeSlotIdsQuery extends AbstractPlugin implements QueryInterface
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
     * MatchTimeSlotIdsQuery constructor.
     *
     * @param int[] $timeSlotIds
     * @param int $limit
     */
    public function __construct(array $timeSlotIds, int $limit)
    {
        $this->limit = $limit;
        $this->query = $this->createSearchQuery($timeSlotIds);
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
     * @param int[] $timeSlotIds
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(array $timeSlotIds)
    {
        $terms = new Terms(TimeSlotIndexMap::ID_TIME_SLOT, $timeSlotIds);

        $boolQuery = (new BoolQuery())
            ->addFilter($terms);

        $query = (new Query())
            ->setQuery($boolQuery)
            ->setSize($this->limit);

        return $query;
    }
}
