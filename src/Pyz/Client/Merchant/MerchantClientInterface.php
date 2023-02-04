<?php
/**
 * Durst - project - MerchantClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:52
 */

namespace Pyz\Client\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GetBranchesRequestTransfer;
use Generated\Shared\Transfer\GetBranchesResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\GetBranchesRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\GetBranchesResponseTransfer
     */
    public function getBranches(GetBranchesRequestTransfer $transfer): GetBranchesResponseTransfer;

    /**
     * Specification:
     *  - Receives the merchant with the given pin from the backend
     *  - If no merchant with the given pin can be found an exception will be thrown
     *
     * @param string $merchantPin
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer;

    /**
     * Specification:
     *  - Returns the branch matching the given id
     *  - If no branch with the id can be found an exception is beeing thrown
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * Specification:
     *  - Returns all branches of a given merchant
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return iterable
     */
    public function getBranchesForMerchant(MerchantTransfer $merchantTransfer): iterable;
}
