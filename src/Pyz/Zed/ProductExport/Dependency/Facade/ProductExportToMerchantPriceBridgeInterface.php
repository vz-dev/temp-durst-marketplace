<?php
/**
 * Durst - project - ProductExportToMerchantPriceBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 17:01
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


interface ProductExportToMerchantPriceBridgeInterface
{
    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\PriceTransfer[]
     */
    public function getPricesForBranch(int $idBranch): array;
}
