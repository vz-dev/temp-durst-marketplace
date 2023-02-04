<?php
/**
 * Durst - project - PriceImportToProductBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:02
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\ProductConcreteTransfer;

interface PriceImportToProductBridgeInterface
{
    /**
     * @param string $concreteSku
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function getProductConcrete(string $concreteSku): ProductConcreteTransfer;
}
