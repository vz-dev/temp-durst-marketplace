<?php
/**
 * Durst - project - DepositMerchantConnectorFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:25
 */

namespace Pyz\Zed\DepositMerchantConnector\Business;

use Generated\Shared\Transfer\BranchTransfer;

interface DepositMerchantConnectorFacadeInterface
{
    /**
     * Specification:
     *  - Returns a list of deposit transfers that are mapped to the given branch
     *  - If no deposit is mapped an empty array will be returned
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): array;
}
