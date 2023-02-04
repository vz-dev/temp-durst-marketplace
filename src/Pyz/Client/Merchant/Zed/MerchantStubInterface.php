<?php
/**
 * Durst - project - MerchantStubInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:20
 */

namespace Pyz\Client\Merchant\Zed;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GetBranchesRequestTransfer;
use Generated\Shared\Transfer\GetBranchesResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\GetBranchesRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\GetBranchesResponseTransfer
     */
    public function getBranches(GetBranchesRequestTransfer $transfer): GetBranchesResponseTransfer;

    /**
     * @param string $merchantPin
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer;

    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return iterable
     */
    public function getBranchesForMerchant(MerchantTransfer $merchantTransfer): iterable;
}
