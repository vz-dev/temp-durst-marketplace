<?php
/**
 * Durst - project - MerchantSaver.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.08.18
 * Time: 15:04
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;


use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class MerchantSaver implements MerchantSaverInterface
{
    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * MerchantSaver constructor.
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     */
    public function __construct(SoftwarePackageQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function saveSoftwarePackageInMerchant(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    )
    {
        $merchantEntity->setFkSoftwarePackage($merchantTransfer->getFkSoftwarePackage());
        $this->removePaymentMethods($merchantTransfer->getFkSoftwarePackage(), $merchantEntity);
    }

    /**
     * @param int $idSoftwarePackage
     * @param SpyMerchant $merchantEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function removePaymentMethods(
        int $idSoftwarePackage,
        SpyMerchant $merchantEntity)
    {
        $paymentMethodsToKeep = $this->getPaymentMethodsToKeep($idSoftwarePackage);

        foreach ($merchantEntity->getSpyBranches() as $spyBranch) {

            foreach ($spyBranch->getSpyBranchToPaymentMethods() as $spyBranchToPaymentMethod) {

                if(false === in_array($spyBranchToPaymentMethod->getFkPaymentMethod(), $paymentMethodsToKeep)){
                    $spyBranchToPaymentMethod->delete();
                }
            }
        }

    }

    /**
     * @param int $idSoftwarePackage
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getPaymentMethodsToKeep(int $idSoftwarePackage) : array
    {
        $softwarePackageToPaymentMethods = $this
            ->queryContainer
            ->querySoftwarePackageToPaymentMethod()
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->find();

        $paymentMethodIds = [];
        foreach ($softwarePackageToPaymentMethods as $softwarePackageToPaymentMethod) {
            $paymentMethodIds[] = $softwarePackageToPaymentMethod->getFkPaymentMethod();
        }

        return $paymentMethodIds;
    }
}