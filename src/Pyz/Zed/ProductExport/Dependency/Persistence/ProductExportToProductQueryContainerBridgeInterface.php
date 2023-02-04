<?php
/**
 * Durst - project - ProductExportToProductQueryContainerBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 11:18
 */

namespace Pyz\Zed\ProductExport\Dependency\Persistence;


use Orm\Zed\Product\Persistence\SpyProductQuery;

interface ProductExportToProductQueryContainerBridgeInterface
{
    /**
     * @return \Orm\Zed\Product\Persistence\SpyProductQuery
     */
    public function queryProduct(): SpyProductQuery;
}
