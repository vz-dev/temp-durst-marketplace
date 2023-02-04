<?php
/**
 * Durst - project - ProductQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.09.18
 * Time: 10:59
 */

namespace Pyz\Zed\Product\Persistence;

use Orm\Zed\Product\Persistence\SpyManufacturerQuery;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface as SprykerProductQueryContainerInterface;

interface ProductQueryContainerInterface extends SprykerProductQueryContainerInterface
{
    /**
     * @return SpyManufacturerQuery
     */
    public function queryManufacturer() : SpyManufacturerQuery;
}