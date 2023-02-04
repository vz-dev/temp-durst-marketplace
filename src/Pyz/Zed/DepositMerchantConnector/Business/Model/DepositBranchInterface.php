<?php
/**
 * Durst - project - DepositBranchInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:49
 */

namespace Pyz\Zed\DepositMerchantConnector\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;

interface DepositBranchInterface
{
    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): array;
}