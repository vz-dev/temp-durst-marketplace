<?php
/**
 * Durst - project - SoftwareFeatureSaver.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.10.18
 * Time: 16:41
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;



use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class SoftwareFeatureSaver implements SoftwareFeatureSaverInterface
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
    public function saveSoftwareFeaturesForSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer)
    {
        foreach ($softwarePackageTransfer->getSoftwareFeatureIds() as $idSoftwareMethod) {

            $this->addSoftwareFeaturesToSoftwarePackage($idSoftwareMethod, $softwarePackageTransfer->getIdSoftwarePackage());
        }

        $this->removeSoftwareFeatures($softwarePackageTransfer->getSoftwareFeatureIds(), $softwarePackageTransfer->getIdSoftwarePackage());
    }

    /**
     * @param int $idSoftwareFeature
     * @param int $idSoftwarePackage
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function addSoftwareFeaturesToSoftwarePackage(
        int $idSoftwareFeature,
        int $idSoftwarePackage
    )
    {
        $entity = $this
            ->queryContainer
            ->querySoftwarePackageToSoftwareFeature()
            ->filterByFkSoftwareFeature($idSoftwareFeature)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->findOneOrCreate();

        if($entity->isNew() || $entity->isModified()){
            $entity->save();
        }
    }

    /**
     * @param array $softwareFeaturesToKeep
     * @param int $idSoftwarePackage
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function removeSoftwareFeatures(array $softwareFeaturesToKeep, int $idSoftwarePackage)
    {
        $entities = $this
            ->queryContainer
            ->querySoftwarePackageToSoftwareFeature()
            ->filterByFkSoftwareFeature($softwareFeaturesToKeep, Criteria::NOT_IN)
            ->filterByFkSoftwarePackage($idSoftwarePackage)
            ->find();

        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

}