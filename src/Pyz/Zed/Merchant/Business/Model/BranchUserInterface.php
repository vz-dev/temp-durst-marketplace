<?php
/**
 * Durst - project - BranchUserInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:45
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchUserTransfer;

interface BranchUserInterface
{
    /**
     * @param int $idBranchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserById(int $idBranchUser): BranchUserTransfer;

    /**
     * @param string $email
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserByEmail(string $email): BranchUserTransfer;

    /**
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function save(BranchUserTransfer $branchUserTransfer): BranchUserTransfer;

    /**
     * @param int $idBranchUser
     * @return bool
     */
    public function deleteBranchUser(int $idBranchUser): bool;

    /**
     * @param int $idBranchUser
     * @return bool
     */
    public function activateBranchUser(int $idBranchUser): bool;

    /**
     * @param int $idBranchUser
     * @return bool
     */
    public function deactivateBranchUser(int $idBranchUser): bool;

    /**
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     */
    public function getCurrentBranchUser(): BranchUserTransfer;

    /**
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return mixed
     */
    public function setCurrentBranchUser(BranchUserTransfer $branchUserTransfer);

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool;

    /**
     * @return void
     */
    public function logout(): void;

    /**
     * @param string $email
     * @return bool
     */
    public function hasActiveBranchUserByEmail(string $email): bool;

    /**
     * @return bool
     */
    public function hasCurrentBranchUser(): bool;

    /**
     * @return void
     */
    public function unsetCurrentBranchUser(): void;

    /**
     * @param int $idBranch
     * @return BranchUserTransfer[]
     */
    public function getBranchUsersByIdBranch(int $idBranch): array;
}
