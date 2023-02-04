<?php
/**
 * Durst - project - SoftwareFeatureHydratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.10.18
 * Time: 16:46
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;

interface SoftwareFeatureHydratorInterface
{
    /**
     * @param DstSoftwarePackage $softwarePackageEntity
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @return void
     */
    public function hydrateSoftwarePackageBySoftwareFeatures(
        DstSoftwarePackage $softwarePackageEntity,
        SoftwarePackageTransfer $softwarePackageTransfer
    );
}