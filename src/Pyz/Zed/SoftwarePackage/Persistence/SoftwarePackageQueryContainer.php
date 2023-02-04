<?php
/**
 * Durst - project - SoftwarePackageQueryContainer.php.
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
use Orm\Zed\Sales\Persistence\Map\DstLicenseTableMap;
use Orm\Zed\Sales\Persistence\SpyBranchToSoftwareFeatureQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class SoftwarePackageQueryContainer
 * @package Pyz\Zed\SoftwarePackage\Persistence
 * @method SoftwarePackagePersistenceFactory getFactory()
 */
class SoftwarePackageQueryContainer extends AbstractQueryContainer implements SoftwarePackageQueryContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @return DstSoftwarePackageQuery
     */
    public function querySoftwarePackage() : DstSoftwarePackageQuery
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return DstSoftwarePackageQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function querySoftwarePackageByCode(string $code): DstSoftwarePackageQuery
    {
        return $this
            ->querySoftwarePackage()
            ->filterByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwarePackage
     * @return DstSoftwarePackageQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function querySoftwarePackageById(int $idSoftwarePackage): DstSoftwarePackageQuery
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageQuery()
            ->filterByIdSoftwarePackage($idSoftwarePackage);
    }

    /**
     * {@inheritdoc}
     *
     * @return DstSoftwarePackageToPaymentMethodQuery
     */
    public function querySoftwarePackageToPaymentMethod(): DstSoftwarePackageToPaymentMethodQuery
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageToPaymentMethodQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return DstSoftwarePackageToSoftwareFeatureQuery
     */
    public function querySoftwarePackageToSoftwareFeature(): DstSoftwarePackageToSoftwareFeatureQuery
    {
        return $this
            ->getFactory()
            ->createSoftwarePackageToSoftwareFeatureQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return DstSoftwareFeatureQuery
     */
    public function querySoftwareFeature() : DstSoftwareFeatureQuery
    {
        return $this
            ->getFactory()
            ->createSoftwareFeatureQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchToSoftwareMethodQuery
     */
    public function queryBranchToSoftwareFeature() : SpyBranchToSoftwareFeatureQuery
    {
        return $this
            ->getFactory()
            ->createBranchToSoftwareFeatureQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return DstSoftwareFeatureQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPossibleSoftwareFeaturesForMerchant(int $idMerchant): DstSoftwareFeatureQuery
    {
        return $this
            ->querySoftwareFeature()
            ->useDstSoftwarePackageToSoftwareFeatureQuery()
                ->useDstSoftwarePackageQuery()
                    ->useSpyMerchantQuery()
                        ->filterByIdMerchant($idMerchant)
                    ->endUse()
                ->endUse()
            ->endUse();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return DstSoftwareFeatureQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function querySoftwareFeatureByCode(string $code): DstSoftwareFeatureQuery
    {
        return $this
            ->querySoftwareFeature()
            ->filterByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @return DstLicenseQuery
     */
    public function queryLicenseKey(): DstLicenseQuery
    {
        return $this
            ->getFactory()
            ->createLicenseKeyQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idLicense
     * @return DstLicenseQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryLicenseKeyById(int $idLicense): DstLicenseQuery
    {
        return $this
            ->queryLicenseKey()
            ->filterByIdLicense($idLicense);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return DstLicenseQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryLicenseKeysByIdBranch(int $idBranch): DstLicenseQuery
    {
        return $this
            ->queryLicenseKey()
            ->filterByFkBranch($idBranch)
            ->orderByRedeemedAt(Criteria::DESC);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @return DstLicenseQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryLicenseKeyCode(string $code, int $idSoftwarePackage): DstLicenseQuery
    {
        $currentDate = (new \DateTime('now'));

        return $this
            ->queryLicenseKey()
            ->filterByLicenseKey($code)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->filterByStatus(DstLicenseTableMap::COL_STATUS_AVAILABLE)
            ->filterByValidFrom($currentDate, Criteria::LESS_EQUAL)
            ->filterByValidTo($currentDate, Criteria::GREATER_THAN)
            ->_or()
            ->filterByValidTo();
    }
}