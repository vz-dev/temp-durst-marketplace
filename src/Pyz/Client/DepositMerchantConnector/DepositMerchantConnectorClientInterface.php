<?php
/**
 * Durst - project - DepositMerchantConnectorClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:40
 */

namespace Pyz\Client\DepositMerchantConnector;


use Generated\Shared\Transfer\BranchTransfer;

interface DepositMerchantConnectorClientInterface
{
    /**
     * Returns a list of all deposits mapped to the given branch
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): iterable;
}