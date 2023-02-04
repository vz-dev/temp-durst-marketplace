<?php
/**
 * Durst - project - ProductQueryContainer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.09.18
 * Time: 10:59
 */

namespace Pyz\Zed\Product\Persistence;

use Orm\Zed\Product\Persistence\SpyManufacturerQuery;
use Spryker\Zed\Product\Persistence\ProductQueryContainer as SprykerProductQueryContainer;

/**
 * Class ProductQueryContainer
 * @package Pyz\Zed\Product\Persistence
 * @method ProductPersistenceFactory getFactory()
 */
class ProductQueryContainer extends SprykerProductQueryContainer implements ProductQueryContainerInterface
{
    /**
     * @return SpyManufacturerQuery
     */
    public function queryManufacturer() : SpyManufacturerQuery
    {
        return $this
            ->getFactory()
            ->createManufacturerQuery();
    }
}