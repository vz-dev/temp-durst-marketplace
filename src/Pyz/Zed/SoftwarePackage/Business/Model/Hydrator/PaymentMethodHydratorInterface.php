<?php
/**
 * Durst - project - PaymentMethodHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 22:29
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;

interface PaymentMethodHydratorInterface
{
    /**
     * @param DstSoftwarePackage $softwarePackageEntity
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return void
     */
    public function hydrateSoftwarePackageByPaymentMethods(
        DstSoftwarePackage $softwarePackageEntity,
        SoftwarePackageTransfer $softwarePackageTransfer
    );
}