<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-08
 * Time: 12:02
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;


use Generated\Shared\Transfer\LicenseTransfer;

interface LicenseKeyInterface
{
    /**
     * @param int $idBranch
     * @return LicenseTransfer[]
     */
    public function getLicenseKeysByIdBranch(int $idBranch): array;

    /**
     * @param int $idBranch
     * @return int
     */
    public function getLicenseUnitsCountByIdBranch(int $idBranch): int;

    /**
     * @param string $code
     * @param int $idSoftwarePackage
     * @return bool
     */
    public function validateLicenseKeyCode(string $code, int $idSoftwarePackage): bool;

    /**
     * @param string $code
     * @param int $idSoftwarePackage
     * @param int $idBranch
     * @return LicenseTransfer
     */
    public function redeemLicenseKeyCode(string $code, int $idSoftwarePackage, int $idBranch): LicenseTransfer;
}