<?php
/**
 * Durst - project - DepositSkuInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 14:06
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DepositSkuTransfer;

interface DepositSkuInterface
{
    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getDepositSkusForBranch(BranchTransfer $branchTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getAcceptedDepositSkusForBranch(BranchTransfer $branchTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\DepositSkuTransfer[] $depositSkuTransfers
     * @return void
     */
    public function updateDepositSkus(iterable $depositSkuTransfers): void;

    /**
     * @param int $idBranch
     * @param int $idDeposit
     * @return \Generated\Shared\Transfer\DepositSkuTransfer
     */
    public function getDepositSkusByDepositIdForBranch(int $idBranch, int $idDeposit): DepositSkuTransfer;
}
