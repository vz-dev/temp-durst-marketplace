<?php
/**
 * Durst - project - PaymentMethodSaver.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.08.18
 * Time: 10:25
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;


use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class PaymentMethodSaver implements PaymentMethodSaverInterface
{
    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * PaymentMethodSaver constructor.
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     */
    public function __construct(
        SoftwarePackageQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @param void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function savePaymentMethodsForSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer)
    {
        foreach ($softwarePackageTransfer->getPaymentMethodIds() as $idPaymentMethod) {

            $this->addPaymentMethodToSoftwarePackage($idPaymentMethod, $softwarePackageTransfer->getIdSoftwarePackage());
        }

        $this->removePaymentMethods($softwarePackageTransfer->getPaymentMethodIds(), $softwarePackageTransfer->getIdSoftwarePackage());
    }

    /**
     * @param int $idPaymentMethod
     * @param int $idSoftwarePackage
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function addPaymentMethodToSoftwarePackage(
        int $idPaymentMethod,
        int $idSoftwarePackage
    )
    {
        $entity = $this
            ->queryContainer
            ->querySoftwarePackageToPaymentMethod()
            ->filterByFkPaymentMethod($idPaymentMethod)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->findOneOrCreate();

        if($entity->isNew() || $entity->isModified()){
            $entity->save();
        }
    }

    /**
     * @param array $paymentMethodsToKeep
     * @param int $idSoftwarePackage
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function removePaymentMethods(array $paymentMethodsToKeep, int $idSoftwarePackage)
    {
        $entities = $this
            ->queryContainer
            ->querySoftwarePackageToPaymentMethod()
            ->filterByFkPaymentMethod($paymentMethodsToKeep, Criteria::NOT_IN)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->find();

        foreach ($entities as $entity) {
            $entity->delete();
        }

        $this->removePaymentMethodsForMerchants($paymentMethodsToKeep, $idSoftwarePackage);
    }

    /**
     * @param array $paymentMethodsToKeep
     * @param int $idSoftwarePackage
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function removePaymentMethodsForMerchants(array $paymentMethodsToKeep, int $idSoftwarePackage)
    {
        $entities = $this
            ->queryContainer
            ->querySoftwarePackageToPaymentMethod()
            ->filterByFkPaymentMethod($paymentMethodsToKeep, Criteria::NOT_IN)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->useSpyPaymentMethodQuery()
                ->useSpyBranchToPaymentMethodQuery()
                    ->useSpyBranchQuery()
                        ->useSpyMerchantQuery()
                            ->filterByFkSoftwarePackage($idSoftwarePackage)
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->find();

        foreach ($entities as $entity) {
            $entity
                ->getSpyPaymentMethod()
                ->getSpyBranchToPaymentMethods()
                ->delete();
        }
    }
}