<?php
/**
 * Durst - project - TouchPersistenceFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.11.18
 * Time: 11:16
 */

namespace Pyz\Zed\Touch\Persistence;

use Orm\Zed\Product\Persistence\SpyProductQuery;
use Spryker\Zed\Touch\Persistence\TouchPersistenceFactory as SprykerTouchPersistenceFactory;

class TouchPersistenceFactory extends SprykerTouchPersistenceFactory
{
    /**
     * @return SpyProductQuery
     */
    public function createProductQuery() : SpyProductQuery
    {
        return SpyProductQuery::create();
    }
}