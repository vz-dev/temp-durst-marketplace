<?php
/**
 * Durst - project - SoftwareFeatureInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 31.10.18
 * Time: 12:46
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\SoftwareFeatureTransfer;

interface SoftwareFeatureInterface
{
    /**
     * @param $idSoftwareFeature
     * @param $idBranch
     * @return void
     */
    public function removeSoftwareFeatureFromBranch($idSoftwareFeature, $idBranch);

    /**
     * @param $idSoftwareFeature
     * @param $branchTransfer
     * @return BranchTransfer
     */
    public function addSoftwareFeatureToBranch($idSoftwareFeature, $branchTransfer) : BranchTransfer;

    /**
     * {@inheritdoc}
     *
     * @param SoftwareFeatureTransfer $paymentMethodTransfer
     * @return SoftwareFeatureTransfer
     */
    public function addSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer;

    /**
     * @param SoftwareFeatureTransfer $softwareFeatureTransfer
     * @return SoftwareFeatureTransfer
     */
    public function updateSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer;

    /**
     * @param int $idSoftwareFeature
     * @return void
     */
    public function removeSoftwareFeature(int $idSoftwareFeature);

    /**
     * @param int $idSoftwareFeature
     * @return SoftwareFeatureTransfer
     */
    public function getSoftwareFeatureById(int $idSoftwareFeature) : SoftwareFeatureTransfer;

    /**
     * @param $idBranch
     * @return SoftwareFeatureTransfer[]
     */
    public function getSoftwareFeaturesByIdBranch(int $idBranch) : array;

    /**
     * @param int $idBranch
     * @param string $softwareFeature
     * @return bool
     */
    public function hasBranchSoftwareFeature(int $idBranch, string $softwareFeature) : bool;

    /**
     * @param array $branchIds
     * @return SoftwareFeatureTransfer[]
     */
    public function getSupportedSoftwareFeaturesForBranches(array $branchIds) : array;

    /**
     * @return SoftwareFeatureTransfer[]
     */
    public function getSoftwareFeatures() : array;

    /**
     * @param string $code
     * @return int
     */
    public function getSoftwareFeatureIdByCode(string $code) : int;

    /**
     * @param string $code
     * @return SoftwareFeatureTransfer
     */
    public function getSoftwareFeatureByCode(string $code) : SoftwareFeatureTransfer;

    /**
     * @param $idMerchant
     * @return SoftwareFeatureTransfer[]
     */
    public function getPossibleSoftwareFeaturesForMerchant(int $idMerchant) : array;
}