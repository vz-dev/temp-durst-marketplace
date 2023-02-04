<?php
/**
 * Durst - project - PriceImportToMerchantPriceBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:01
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\PriceTransfer;

interface PriceImportToMerchantPriceBridgeInterface
{
    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\PriceTransfer[]
     */
    public function getPricesForBranch(int $idBranch): array;

    /**
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     */
    public function importPriceForBranch(PriceTransfer $priceTransfer);

    /**
     * @param int $idPrice
     * @param int $idBranch
     */
    public function removePriceFromBranch(
        int $idPrice,
        int $idBranch
    );
}
