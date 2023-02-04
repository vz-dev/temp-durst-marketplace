<?php
/**
 * Durst - project - AccountingToMerchantBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:31
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use Generated\Shared\Transfer\MerchantTransfer;

interface AccountingToMerchantBridgeInterface
{
    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\BranchTransfer[]
     */
    public function getBranchesByIdMerchant(int $idMerchant): array;

    /**
     * @return \Generated\Shared\Transfer\MerchantTransfer[]
     */
    public function getMerchants(): array;

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer;
}
