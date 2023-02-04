<?php
/**
 * Durst - project - SoftwarePackagePersistenceFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:34
 */

namespace Pyz\Zed\SoftwarePackage\Persistence;


use Orm\Zed\Sales\Persistence\DstLicenseQuery;
use Orm\Zed\Sales\Persistence\DstSoftwareFeatureQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageToPaymentMethodQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageToSoftwareFeatureQuery;
use Orm\Zed\Sales\Persistence\SpyBranchToSoftwareFeatureQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class SoftwarePackagePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstSoftwarePackageQuery
     */
    public function createSoftwarePackageQuery() : DstSoftwarePackageQuery
    {
        return DstSoftwarePackageQuery::create();
    }

    /**
     * @return DstSoftwarePackageToPaymentMethodQuery
     */
    public function createSoftwarePackageToPaymentMethodQuery() : DstSoftwarePackageToPaymentMethodQuery
    {
        return DstSoftwarePackageToPaymentMethodQuery::create();
    }

    /**
     * @return DstSoftwareFeatureQuery
     */
    public function createSoftwareFeatureQuery() : DstSoftwareFeatureQuery
    {
        return DstSoftwareFeatureQuery::create();
    }

    /**
     * @return DstSoftwarePackageToSoftwareFeatureQuery
     */
    public function createSoftwarePackageToSoftwareFeatureQuery() : DstSoftwarePackageToSoftwareFeatureQuery
    {
        return DstSoftwarePackageToSoftwareFeatureQuery::create();
    }

    /**
     * @return SpyBranchToSoftwareFeatureQuery
     */
    public function createBranchToSoftwareFeatureQuery() : SpyBranchToSoftwareFeatureQuery
    {
        return SpyBranchToSoftwareFeatureQuery::create();
    }

    /**
     * @return DstLicenseQuery
     */
    public function createLicenseKeyQuery(): DstLicenseQuery
    {
        return DstLicenseQuery::create();
    }
}