<?php
/**
 * Durst - project - PaymentMethodHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 22:29
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;

class PaymentMethodHydrator implements PaymentMethodHydratorInterface
{
    /**
     * @param DstSoftwarePackage $softwarePackageEntity
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function hydrateSoftwarePackageByPaymentMethods(
        DstSoftwarePackage $softwarePackageEntity,
        SoftwarePackageTransfer $softwarePackageTransfer
    )
    {
        $paymentMethodIds = [];
        foreach ($softwarePackageEntity->getDstSoftwarePackageToPaymentMethodsJoinSpyPaymentMethod() as $packageToPaymentMethod) {

            $paymentMethodEntity = $packageToPaymentMethod->getSpyPaymentMethod();
            $softwarePackageTransfer->addPaymentMethods($this->entityToTransfer($paymentMethodEntity));
            $paymentMethodIds[] = $paymentMethodEntity->getIdPaymentMethod();
        }

        $softwarePackageTransfer->setPaymentMethodIds($paymentMethodIds);
    }

    /**
     * @param SpyPaymentMethod $entity
     * @return PaymentMethodTransfer
     */
    protected function entityToTransfer(SpyPaymentMethod $entity) : PaymentMethodTransfer
    {
        return (new PaymentMethodTransfer())
            ->fromArray($entity->toArray(), true);
    }
}