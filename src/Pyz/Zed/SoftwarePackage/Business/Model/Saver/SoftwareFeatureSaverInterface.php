<?php
/**
 * Durst - project - SoftwareFeatureSaverInterface.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.10.18
 * Time: 16:42
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;


use Generated\Shared\Transfer\SoftwarePackageTransfer;

interface SoftwareFeatureSaverInterface
{
    /**
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return void
     */
    public function saveSoftwareFeaturesForSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer);
}