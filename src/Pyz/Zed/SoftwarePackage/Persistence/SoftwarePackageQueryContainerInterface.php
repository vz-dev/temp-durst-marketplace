<?php
/**
 * Durst - project - SoftwarePackageQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:33
 */

namespace Pyz\Zed\SoftwarePackage\Persistence;


use Orm\Zed\Sales\Persistence\DstLicenseQuery;
use Orm\Zed\Sales\Persistence\DstSoftwareFeatureQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageToPaymentMethodQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageToSoftwareFeatureQuery;
use Orm\Zed\Sales\Persistence\SpyBranchToSoftwareFeatureQuery;

interface SoftwarePackageQueryContainerInterface
{
    /**
     * @return DstSoftwarePackageQuery
     */
    public function querySoftwarePackage() : DstSoftwarePackageQuery;

    /**
     * @param string $code
     * @return DstSoftwarePackageQuery
     */
    public function querySoftwarePackageByCode(string $code) : DstSoftwarePackageQuery;

    /**
     * @param int $idSoftwarePackage
     * @return DstSoftwarePackageQuery
     */
    public function querySoftwarePackageById(int $idSoftwarePackage) : DstSoftwarePackageQuery;

    /**
     * @return DstSoftwarePackageToPaymentMethodQuery
     */
    public function querySoftwarePackageToPaymentMethod() : DstSoftwarePackageToPaymentMethodQuery;

    /**
     * @return DstSoftwareFeatureQuery
     */
    public function querySoftwareFeature() : DstSoftwareFeatureQuery;

    /**
     * @return DstSoftwarePackageToSoftwareFeatureQuery
     */
    public function querySoftwarePackageToSoftwareFeature(): DstSoftwarePackageToSoftwareFeatureQuery;

    /**
     * @return SpyBranchToSoftwareFeatureQuery
     */
    public function queryBranchToSoftwareFeature() : SpyBranchToSoftwareFeatureQuery;

    /**
     * @param int $idMerchant
     * @return DstSoftwareFeatureQuery
     */
    public function queryPossibleSoftwareFeaturesForMerchant(int $idMerchant): DstSoftwareFeatureQuery;

    /**
     * @param string $code
     * @return DstSoftwareFeatureQuery
     */
    public function querySoftwareFeatureByCode(string $code): DstSoftwareFeatureQuery;

    /**
     * @return DstLicenseQuery
     */
    public function queryLicenseKey(): DstLicenseQuery;

    /**
     * @param int $idLicense
     * @return DstLicenseQuery
     */
    public function queryLicenseKeyById(int $idLicense): DstLicenseQuery;

    /**
     * @param int $idBranch
     * @return DstLicenseQuery
     */
    public function queryLicenseKeysByIdBranch(int $idBranch): DstLicenseQuery;

    /**
     * @param string $code
     * @param int $idSoftwarePackage
     * @return DstLicenseQuery
     */
    public function queryLicenseKeyCode(string $code, int $idSoftwarePackage): DstLicenseQuery;
}