<?php
/**
 * Durst - project - SoftwarePackageInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:07
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;

use Generated\Shared\Transfer\SoftwarePackageTransfer;

interface SoftwarePackageInterface
{
    /**
     * @param string $code
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageByCode(string $code): SoftwarePackageTransfer;

    /**
     * @param int $idSoftwarePackage
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageById(int $idSoftwarePackage): SoftwarePackageTransfer;

    /**
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer[]
     */
    public function getSoftwarePackages(): array;

    /**
     * @param \Generated\Shared\Transfer\SoftwarePackageTransfer $softwarePackageTransfer
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function saveSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer): SoftwarePackageTransfer;

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function activateSoftwarePackage(int $idSoftwarePackage);

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function deactivateSoftwarePackage(int $idSoftwarePackage);

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function deleteSoftwarePackage(int $idSoftwarePackage);

    /**
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantWholesalePackage(int $idMerchant): bool;

    /**
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantRetailPackage(int $idMerchant): bool;
}
