<?php
/**
 * Durst - project - SalesToIntegraInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 11.11.20
 * Time: 15:47
 */

namespace Pyz\Zed\Sales\Dependency\Facade;


use Generated\Shared\Transfer\IntegraCredentialsTransfer;

interface SalesToIntegraInterface
{
    /**
     * @param int $idBranch
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool;

    /**
     * @param int $idBranch
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer;
}
