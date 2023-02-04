<?php
/**
 * Durst - project - ProductExportToProductBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 13:28
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


use Generated\Shared\Transfer\ProductConcreteTransfer;

interface ProductExportToProductBridgeInterface
{
    /**
     * @param int $idProduct
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function findProductConcreteById(int $idProduct): ProductConcreteTransfer;
}
