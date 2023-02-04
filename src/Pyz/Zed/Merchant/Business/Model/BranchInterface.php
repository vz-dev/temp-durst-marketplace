<?php
/**
 * Durst - project - BranchInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:17
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;

interface BranchInterface
{
    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function save(BranchTransfer $branchTransfer): BranchTransfer;

    /**
     * @param string $code
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchByCode(string $code): BranchTransfer;

    /**
     * @param string $name
     * @return bool
     */
    public function hasBranchByName(string $name): bool;

    /**
     * @param int $idBranch
     * @return bool
     */
    public function hasBranchById(int $idBranch): bool;

    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * @param int $idBranch
     * @param int $orderedUnits
     */
    public function sumUpOrderedUnitsToBranchById(int $idBranch, int $orderedUnits): void;

    /**
     * @param int $idMerchant
     * @return array
     */
    public function getBranchesByIdMerchant(int $idMerchant): array;

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchByIdMerchant(int $idMerchant): BranchTransfer;

    /**
     * @return bool
     */
    public function hasCurrentBranch(): bool;

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branch
     * @return mixed
     */
    public function setCurrentBranch(BranchTransfer $branch);

    /**
     * @return \Generated\Shared\Transfer\BranchTransfer|null
     */
    public function getCurrentBranch(): ?BranchTransfer;

    /**
     * @return void
     */
    public function logout(): void;

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isIgnorablePath(string $bundle, string $controller, string $action): bool;

    /**
     * @return bool
     */
    public function branchesAreImported(): bool;

    /**
     * @return array
     */
    public function getBranches(): array;

    /**
     * @param string $zipCode
     * @return array
     */
    public function getBranchesByZipCode(string $zipCode): array;

    /**
     * @param int $idBranch
     * @return void
     */
    public function deleteBranch(int $idBranch);

    /**
     * @param int $id
     * @param string $zipCode
     * @return \Generated\Shared\Transfer\BranchTransfer|null
     */
    public function getBranchByIdAndZipCode(int $id, string $zipCode): ?BranchTransfer;

    /**
     * @param int $idBranch
     * @return void
     */
    public function restoreBranch(int $idBranch): void;

    /**
     * @return void
     */
    public function activateCurrentBranch();

    /**
     * @return void
     */
    public function deactivateCurrentBranch();

    /**
     * @param string $hash
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchByHash(string $hash): BranchTransfer;

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return string
     */
    public function getHashForBranch(BranchTransfer $branchTransfer): string;

    /**
     * @param int $idBranch
     * @param int $licenseUnits
     * @return void
     */
    public function sumUpLicenseUnitsToBranchById(int $idBranch, int $licenseUnits);
}
