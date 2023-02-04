<?php
/**
 * Durst - project - SoftwarePackageFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:31
 */

namespace Pyz\Zed\SoftwarePackage\Business;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\LicenseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;

interface SoftwarePackageFacadeInterface
{
    /**
     * Hydrates the merchant transfer by the matching reference to a software package transfer
     *
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     */
    public function hydrateMerchantBySoftwarePackage(SpyMerchant $merchantEntity, MerchantTransfer $merchantTransfer);

    /**
     * This ensures that the property fkSoftwarePackage in the transfer object gets persisted
     * in the entity object.
     *
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     */
    public function saveSoftwarePackageInMerchant(SpyMerchant $merchantEntity, MerchantTransfer $merchantTransfer);

    /**
     * Sets the status of the software package to "active"
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function activateSoftwarePackage(int $idSoftwarePackage);

    /**
     * Sets the status of the software package to "inactive"
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function deactivateSoftwarePackage(int $idSoftwarePackage);

    /**
     * Sets the status of the software package to "deleted"
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function deleteSoftwarePackage(int $idSoftwarePackage);

    /**
     * Saves the data contained in the transfer object to the database and returns a
     * fully hydrated transfer object.
     *
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return SoftwarePackageTransfer
     */
    public function updateSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer) : SoftwarePackageTransfer;

    /**
     * Returns a transfer object representing the software package
     * matching the given code from the database.
     * If no software package with the given code can be found an
     * exception will be thrown.
     *
     * @param string $code
     * @return SoftwarePackageTransfer
     */
    public function getSoftwarePackageByCode(string $code) : SoftwarePackageTransfer;

    /**
     * Returns a transfer object representing the software package
     * matching the given id from the database.
     * If no software package with the given code can be found an
     * exception will be thrown.
     *
     * @param int $idSoftwarePackage
     * @return SoftwarePackageTransfer
     */
    public function getSoftwarePackageById(int $idSoftwarePackage) : SoftwarePackageTransfer;

    /**
     * Returns a transfer object representing the software feature
     * matching the given id from the database.
     * If no software package with the given code can be found an
     * exception will be thrown.
     *
     * @param int $idSoftwareFeature
     * @return SoftwareFeatureTransfer
     */
    public function getSoftwareFeatureById(int $idSoftwareFeature): SoftwareFeatureTransfer;

    /**
     * Saves the data provided by the SoftwareFeatureTransfer and saves it to the database
     * A fully hydrated SoftwareFeatureTransfer is returned
     *
     * @param SoftwareFeatureTransfer $softwareFeatureTransfer
     * @return SoftwareFeatureTransfer
     */
    public function addSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer;

    /**
     * Saves the data contained in the transfer object to the database and returns a
     * fully hydrated transfer object.
     *
     * @param SoftwareFeatureTransfer $softwareFeatureTransfer
     * @return SoftwareFeatureTransfer
     */
    public function updateSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer;

    /**
     * Removes the Software Feature for the branch with the passed id from the
     * branchtosoftwarefeature table
     *
     * @param int $id
     * @param int $idBranch
     */
    public function removeSoftwareFeatureFromBranch(int $id, int $idBranch);

    /**
     * Returns a transfer for each supported SoftwareFeature for all provided branch ids
     *
     * @param array $branchIds
     * @return SoftwareFeatureTransfer[]
     */
    public function getSupportedSoftwareFeaturesForBranches(array $branchIds) : array;

    /**
     * Returns all possible software feature transfers for the merchant with passed id
     *
     * @param int $idMerchant
     * @return SoftwareFeatureTransfer[]
     */
    public function getPossibleSoftwareFeaturesForMerchant(int $idMerchant) : array;

    /**
     * Adds the SoftwareFeature with the given id to the Branch provided. Saves the entrs to the
     * BranchToSoftwareFeature table and returns a hydrated BranchTransfer
     *
     * @param int $idSoftwareFeature
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     */
    public function addSoftwareFeatureToBranch(int $idSoftwareFeature, BranchTransfer $branchTransfer) :BranchTransfer;

    /**
     * Returns an array of all possible SoftwareFeatures for the given branch
     *
     * @param int $idBranch
     * @return SoftwareFeatureTransfer[]
     */
    public function getSoftwareFeaturesByIdBranch(int $idBranch) : array;

    /**
     * Return an array of all redeemed licenses for the given branch
     *
     * @param int $idBranch
     * @return LicenseTransfer[]
     */
    public function getLicenseKeysByIdBranch(int $idBranch): array;

    /**
     * Count all license units for a given branch
     *
     * @param int $idBranch
     * @return int
     */
    public function getLicenseUnitsCountByIdBranch(int $idBranch): int;

    /**
     * Check, if a given code is valid in respect of the given software package
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @return bool
     */
    public function validateLicenseKeyCode(string $code, int $idSoftwarePackage): bool;

    /**
     * Redeem the given code for the given branch in the given software package
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @param int $idBranch
     * @return LicenseTransfer|null
     */
    public function redeemLicenseKeyCode(string $code, int $idSoftwarePackage, int $idBranch): ?LicenseTransfer;

    /**
     * Specification:
     *  - Returns true if the merchant matching the given id is linked to the software package
     *    with the code @see \Pyz\Shared\SoftwarePackage\SoftwarePackageConstants::SOFTWARE_PACKAGE_WHOLESALE_CODE
     *  - Returns false if no merchant matches the id, the merchant doesn't have a software package or the merchant
     *    is linked to a different software package
     *
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantWholesalePackage(int $idMerchant): bool;

    /**
     * Specification:
     *  - Returns true if the merchant matching the given id is linked to the software package
     *    with the code @see SoftwarePackageConstants::SOFTWARE_PACKAGE_RETAIL_CODE
     *  - Returns false if no merchant matches the id, the merchant doesn't have a software package or the merchant
     *    is linked to a different software package
     *
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantRetailPackage(int $idMerchant): bool;
}
