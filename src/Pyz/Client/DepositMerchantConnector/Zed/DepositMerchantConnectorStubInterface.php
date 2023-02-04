<?php
/**
 * Durst - project - DepositMerchantConnectorStubInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:44
 */

namespace Pyz\Client\DepositMerchantConnector\Zed;


use Generated\Shared\Transfer\BranchTransfer;

interface DepositMerchantConnectorStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): iterable;
}