<?php
/**
 * Durst - project - SoftwareFeatureHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.10.18
 * Time: 16:46
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Sales\Persistence\DstSoftwareFeature;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;

class SoftwareFeatureHydrator implements SoftwareFeatureHydratorInterface
{
    /**
     * @param DstSoftwarePackage $softwarePackageEntity
     * @param SoftwarePackageTransfer $softwarePackageTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function hydrateSoftwarePackageBySoftwareFeatures(
        DstSoftwarePackage $softwarePackageEntity,
        SoftwarePackageTransfer $softwarePackageTransfer
    )
    {
        $softwareFeatureIds = [];
        foreach ($softwarePackageEntity->getDstSoftwarePackageToSoftwareFeaturesJoinDstSoftwareFeature() as $packageToSoftwareFeature) {
            $softwareFeatureEntity = $packageToSoftwareFeature->getDstSoftwareFeature();
            $softwarePackageTransfer->addSoftwareFeatures($this->entityToTransfer($softwareFeatureEntity));
            $softwareFeatureIds[] = $softwareFeatureEntity->getIdSoftwareFeature();
        }

        $softwarePackageTransfer->setSoftwareFeatureIds($softwareFeatureIds);
    }

    /**
     * @param DstSoftwareFeature $entity
     * @return SoftwareFeatureTransfer
     */
    protected function entityToTransfer(DstSoftwareFeature $entity) : SoftwareFeatureTransfer
    {
        return (new SoftwareFeatureTransfer())
            ->fromArray($entity->toArray(), true);
    }
}