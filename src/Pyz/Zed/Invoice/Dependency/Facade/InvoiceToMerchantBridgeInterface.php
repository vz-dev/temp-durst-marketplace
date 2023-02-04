<?php
/**
 * Durst - project - InvoiceToMerchantFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:07
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;


use Generated\Shared\Transfer\BranchTransfer;

interface InvoiceToMerchantBridgeInterface
{
    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * @return array
     */
    public function getBranches() : array;
}
