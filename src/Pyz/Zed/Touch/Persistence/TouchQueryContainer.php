<?php
/**
 * Durst - project - TouchQueryContainer.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.11.18
 * Time: 11:20
 */

namespace Pyz\Zed\Touch\Persistence;

use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Orm\Zed\Touch\Persistence\SpyTouchSearchQuery;
use Spryker\Zed\Touch\Persistence\TouchQueryContainer as SprykerTouchQueryContainer;

/**
 * @method \Pyz\Zed\Touch\Persistence\TouchPersistenceFactory getFactory()
 */
class TouchQueryContainer extends SprykerTouchQueryContainer implements TouchQueryContainerInterface
{
    /**
     * @param int $categoryId
     * @return SpyProductQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryProductsByCategoryId(int $categoryId): SpyProductQuery
    {
        return $this->getFactory()
            ->createProductQuery()
            ->useSpyProductAbstractQuery()
                ->useSpyProductCategoryQuery()
                    ->filterByFkCategory($categoryId)
                ->endUse()
            ->endUse();
    }

    /**
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouch(): SpyTouchQuery
    {
        return $this
            ->getFactory()
            ->createTouchQuery();
    }

    /**
     * @return \Orm\Zed\Touch\Persistence\SpyTouchSearchQuery
     */
    public function queryTouchSearch(): SpyTouchSearchQuery
    {
        return $this
            ->getFactory()
            ->createTouchSearchQuery();
    }
}
