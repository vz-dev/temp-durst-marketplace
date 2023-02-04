<?php
/**
 * Durst - project - TouchQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.11.18
 * Time: 11:21
 */

namespace Pyz\Zed\Touch\Persistence;

use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Orm\Zed\Touch\Persistence\SpyTouchSearchQuery;
use Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface as SprykerTouchQueryContainerInterface;

interface TouchQueryContainerInterface extends SprykerTouchQueryContainerInterface
{
    /**
     * @param int $productId
     * @return SpyProductQuery
     */
    public function queryProductsByCategoryId(int $productId): SpyProductQuery;

    /**
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouch(): SpyTouchQuery;

    /**
     * @return \Orm\Zed\Touch\Persistence\SpyTouchSearchQuery
     */
    public function queryTouchSearch(): SpyTouchSearchQuery;
}
