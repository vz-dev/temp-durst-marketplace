<?php
/**
 * Durst - project - ProductPersistenceFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.09.18
 * Time: 11:00
 */

namespace Pyz\Zed\Product\Persistence;

use Orm\Zed\Product\Persistence\SpyManufacturerQuery;
use Spryker\Zed\Product\Persistence\ProductPersistenceFactory as SprkyerProductPersistenceFactory;

class ProductPersistenceFactory extends SprkyerProductPersistenceFactory
{
    /**
     * @return SpyManufacturerQuery
     */
    public function createManufacturerQuery() : SpyManufacturerQuery
    {
        return SpyManufacturerQuery::create();
    }
}