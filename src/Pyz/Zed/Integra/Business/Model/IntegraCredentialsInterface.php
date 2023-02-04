<?php
/**
 * Durst - project - IntegraCredentialsInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 10:20
 */

namespace Pyz\Zed\Integra\Business\Model;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;

interface IntegraCredentialsInterface
{
    /**
     * @param IntegraCredentialsTransfer $transfer
     *
     * @return void
     */
    public function save(IntegraCredentialsTransfer $transfer): void;

    /**
     * @param int $idCredentialsTransfer
     *
     * @return void
     */
    public function remove(int $idCredentialsTransfer): void;

    /**
     * @return array
     */
    public function getBranchIdsThatUseIntegra(): array;

    /**
     * @param int $idBranch
     *
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer;

    /**
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool;
}
