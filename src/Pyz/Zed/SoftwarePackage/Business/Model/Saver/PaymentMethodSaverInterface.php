<?php
/**
 * Durst - project - PaymentMethodSaverInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.08.18
 * Time: 10:13
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;


use Generated\Shared\Transfer\SoftwarePackageTransfer;

interface PaymentMethodSaverInterface
{
    /**
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return void
     */
    public function savePaymentMethodsForSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer);
}