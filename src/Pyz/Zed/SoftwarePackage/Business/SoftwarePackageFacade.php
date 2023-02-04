<?php
/**
 * Durst - project - SoftwarePackageFacade.php.
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
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class SoftwarePackageFacade
 * @package Pyz\Zed\SoftwarePackage\Business
 * @method \Pyz\Zed\SoftwarePackage\Business\SoftwarePackageBusinessFactory getFactory()
 */
class SoftwarePackageFacade extends AbstractFacade implements SoftwarePackageFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchantEntity
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return void
     */
    public function hydrateMerchantBySoftwarePackage(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    ) {
        $this
            ->getFactory()
            ->createMerchantHydrator()
            ->hydrateMerchantBySoftwarePackage($merchantEntity, $merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function activateSoftwarePackage(int $idSoftwarePackage)
    {
        $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->activateSoftwarePackage($idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function deactivateSoftwarePackage(int $idSoftwarePackage)
    {
        $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->deactivateSoftwarePackage($idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwarePackage
     * @return void
     */
    public function deleteSoftwarePackage(int $idSoftwarePackage)
    {
        $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->deleteSoftwarePackage($idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\SoftwarePackageTransfer $softwarePackageTransfer
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function updateSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer): SoftwarePackageTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->saveSoftwarePackage($softwarePackageTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageByCode(string $code): SoftwarePackageTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->getSoftwarePackageByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwarePackage
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageById(int $idSoftwarePackage): SoftwarePackageTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->getSoftwarePackageById($idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchantEntity
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return void
     */
    public function saveSoftwarePackageInMerchant(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    ) {
        $this
            ->getFactory()
            ->createMerchantSaver()
            ->saveSoftwarePackageInMerchant($merchantEntity, $merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer
     */
    public function getSoftwareFeatureById(int $idSoftwareFeature): SoftwareFeatureTransfer
    {
        return $this
           ->getFactory()
           ->createSoftwareFeatureModel()
           ->getSoftwareFeatureById($idSoftwareFeature);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\SoftwareFeatureTransfer $softwareFeatureTransfer
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer
     */
    public function updateSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer): SoftwareFeatureTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->updateSoftwareFeature($softwareFeatureTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\SoftwareFeatureTransfer $softwareFeatureTransfer
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer
     */
    public function addSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer): SoftwareFeatureTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->addSoftwareFeature($softwareFeatureTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @param int $idBranch
     */
    public function removeSoftwareFeatureFromBranch(int $id, int $idBranch)
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->removeSoftwareFeatureFromBranch($id, $idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer[]
     */
    public function getSupportedSoftwareFeaturesForBranches(array $branchIds): array
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->getSupportedSoftwareFeaturesForBranches($branchIds);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer[]
     */
    public function getPossibleSoftwareFeaturesForMerchant(int $idMerchant): array
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->getPossibleSoftwareFeaturesForMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function addSoftwareFeatureToBranch(int $idSoftwareFeature, BranchTransfer $branchTransfer): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->addSoftwareFeatureToBranch($idSoftwareFeature, $branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\SoftwareFeatureTransfer[]
     */
    public function getSoftwareFeaturesByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureModel()
            ->getSoftwareFeaturesByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\LicenseTransfer[]
     */
    public function getLicenseKeysByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createLicenseKeyModel()
            ->getLicenseKeysByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return int
     */
    public function getLicenseUnitsCountByIdBranch(int $idBranch): int
    {
        return $this
            ->getFactory()
            ->createLicenseKeyModel()
            ->getLicenseUnitsCountByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @return bool
     */
    public function validateLicenseKeyCode(string $code, int $idSoftwarePackage): bool
    {
        return $this
            ->getFactory()
            ->createLicenseKeyModel()
            ->validateLicenseKeyCode($code, $idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\LicenseTransfer|null
     */
    public function redeemLicenseKeyCode(string $code, int $idSoftwarePackage, int $idBranch): ?LicenseTransfer
    {
        return $this
            ->getFactory()
            ->createLicenseKeyModel()
            ->redeemLicenseKeyCode($code, $idSoftwarePackage, $idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantWholesalePackage(int $idMerchant): bool
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->hasMerchantWholesalePackage($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     *
     * @return bool
     */
    public function hasMerchantRetailPackage(int $idMerchant): bool
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageModel()
            ->hasMerchantRetailPackage($idMerchant);
    }
}
