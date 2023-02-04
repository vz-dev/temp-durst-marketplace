<?php
/**
 * Durst - project - SoftwareFeature.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 31.10.18
 * Time: 12:46
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Orm\Zed\Sales\Persistence\DstSoftwareFeature;
use Orm\Zed\Sales\Persistence\SpyBranchToSoftwareFeature;
use Pyz\Zed\SoftwarePackage\Business\Exception\SoftwareFeatureExistsException;
use Pyz\Zed\SoftwarePackage\Business\Exception\SoftwareFeatureNotFoundException;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class SoftwareFeature implements SoftwareFeatureInterface
{
    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * SoftwareFeature constructor.
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     */
    public function __construct(
        SoftwarePackageQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @param int $idBranch
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeSoftwareFeatureFromBranch($idSoftwareFeature, $idBranch)
    {
        $entity = $this
            ->queryContainer
            ->queryBranchToSoftwareFeature()
            ->filterByFkBranch($idBranch)
            ->filterByFkSoftwareFeature($idSoftwareFeature)
            ->findOne();

        if($entity !== null){
            $entity->delete();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws SoftwareFeatureNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addSoftwareFeatureToBranch($idSoftwareFeature, $branchTransfer) : BranchTransfer
    {
        if($this->hasSoftwareFeatureByIdSoftwareFeatureAndIdBranch($idSoftwareFeature, $branchTransfer->getIdBranch())){
            return $branchTransfer;
        }

        $entity = new SpyBranchToSoftwareFeature();
        $entity->setFkBranch($branchTransfer->getIdBranch());
        $entity->setFkSoftwareFeature($idSoftwareFeature);
        $entity->save();

        $branchTransfer->addSoftwareFeatures(
            $this
                ->getSoftwareFeatureById($idSoftwareFeature)
        );

        return $branchTransfer;
    }

    /**
     * {@inheritdoc}
     *
     * @param SoftwareFeatureTransfer $paymentMethodTransfer
     * @return SoftwareFeatureTransfer
     * @throws SoftwareFeatureExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer
    {
        if($softwareFeatureTransfer->getIdSoftwareFeature() !== null
            && $this->hasSoftwareFeatureById($softwareFeatureTransfer->getIdSoftwareFeature())){
            throw new SoftwareFeatureExistsException(
                sprintf(
                    SoftwareFeatureExistsException::EXISTS_ID,
                    $softwareFeatureTransfer->getIdSoftwareFeature()
                )
            );
        }

        $entity = new DstSoftwareFeature();
        $entity->setName($softwareFeatureTransfer->getName());
        $entity->setCode($softwareFeatureTransfer->getCode());
        $entity->setDescription($softwareFeatureTransfer->getDescription());
        $entity->save();

        return $this->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param SoftwareFeatureTransfer $paymentMethodTransfer
     * @return SoftwareFeatureTransfer
     * @throws SoftwareFeatureNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updateSoftwareFeature(SoftwareFeatureTransfer $softwareFeatureTransfer) : SoftwareFeatureTransfer
    {
        if($softwareFeatureTransfer->getIdSoftwareFeature() === null){
            throw new SoftwareFeatureNotFoundException(
                SoftwareFeatureNotFoundException::NO_ID
            );
        }
        if($this->hasSoftwareFeatureById($softwareFeatureTransfer->getIdSoftwareFeature()) !== true){
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::NOT_FOUND,
                    $softwareFeatureTransfer->getIdSoftwareFeature()
                )
            );
        }

        $entity = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->filterByIdSoftwareFeature($softwareFeatureTransfer->getIdSoftwareFeature())
            ->findOne();

        if($entity === null){
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::NOT_FOUND,
                    $softwareFeatureTransfer->getIdSoftwareFeature()
                )
            );
        }

        $entity->setName($softwareFeatureTransfer->getName());
        $entity->setCode($softwareFeatureTransfer->getCode());
        $entity->setDescription($softwareFeatureTransfer->getDescription());
        $entity->save();

        return $this
            ->entityToTransfer($entity);

    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @return void
     * @throws SoftwareFeatureNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeSoftwareFeature(int $idSoftwareFeature)
    {
        if($this->hasSoftwareFeatureById($idSoftwareFeature) !== true){
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::NOT_FOUND,
                    $idSoftwareFeature
                )
            );
        }

        $entity = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->filterByIdSoftwareFeature($idSoftwareFeature)
            ->findOne();

        $entity->delete();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSoftwareFeature
     * @return SoftwareFeatureTransfer
     * @throws SoftwareFeatureNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getSoftwareFeatureById(int $idSoftwareFeature) : SoftwareFeatureTransfer
    {
        $entity = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->filterByIdSoftwareFeature($idSoftwareFeature)
            ->findOne();

        if($entity === null){
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::NOT_FOUND,
                    $idSoftwareFeature
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return SoftwareFeatureTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getSoftwareFeaturesByIdBranch(int $idBranch) : array
    {
        $entities = $this
            ->queryContainer
            ->queryBranchToSoftwareFeature()
            ->filterByFkBranch($idBranch)
            ->find();

        $transfers = [];
        foreach($entities as $entity){
            $transfers[] = $this->entityToTransfer($entity->getDstSoftwareFeature());
        }

        return $transfers;
    }

    /**
     * @param int $idSoftwareFeature
     * @param int $idBranch
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function hasSoftwareFeatureByIdSoftwareFeatureAndIdBranch(int $idSoftwareFeature, $idBranch) : bool
    {
        return ($this
                ->queryContainer
                ->queryBranchToSoftwareFeature()
                ->filterByFkBranch($idBranch)
                ->filterByFkSoftwareFeature($idSoftwareFeature)
                ->count() > 0);
    }

    /**
     * @param int $idSoftwareFeature
     * @return bool
     */
    protected function hasSoftwareFeatureById($idSoftwareFeature) : bool
    {
        return ($this
                ->queryContainer
                ->querySoftwareFeature()
                ->findByIdSoftwareFeature($idSoftwareFeature)
                ->count() > 0);
    }

    /**
     * @param DstSoftwareFeature $entity
     * @return SoftwareFeatureTransfer
     */
    protected function entityToTransfer(DstSoftwareFeature $entity) : SoftwareFeatureTransfer
    {
        return (new SoftwareFeatureTransfer())
            ->fromArray($entity->toArray());
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param string $softwareFeature
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasBranchSoftwareFeature(int $idBranch, string $softwareFeature) : bool
    {
        return $this
                ->queryContainer
                ->queryBranchToSoftwareFeature()
                ->filterByFkBranch($idBranch)
                ->useDstSoftwareFeatureQuery()
                    ->filterByCode($softwareFeature)
                ->endUse()
                ->count() > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return SoftwareFeatureTransfer[]
     */
    public function getSupportedSoftwareFeaturesForBranches(array $branchIds) : array
    {
        $entities = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->useSpyBranchToSoftwareFeatureQuery()
            ->filterByFkBranch_In($branchIds)
            ->endUse()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @return SoftwareFeatureTransfer[]
     */
    public function getSoftwareFeatures() : array
    {
        $entities = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return int
     * @throws SoftwareFeatureNotFoundException
     */
    public function getSoftwareFeatureIdByCode(string $code) : int
    {
        $entity = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->findOneByCode($code);

        if($entity === null){
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::CODE_NOT_FOUND,
                    $code
                )
            );
        }

        return $entity->getIdSoftwareFeature();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return array
     */
    public function getPossibleSoftwareFeaturesForMerchant(int $idMerchant) : array
    {
        $entities = $this
            ->queryContainer
            ->queryPossibleSoftwareFeaturesForMerchant($idMerchant);

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return SoftwareFeatureTransfer
     * @throws SoftwareFeatureNotFoundException
     */
    public function getSoftwareFeatureByCode(string $code) : SoftwareFeatureTransfer
    {
        $entity = $this
            ->queryContainer
            ->querySoftwareFeatureByCode($code)
            ->findOne();

        if($entity === null) {
            throw new SoftwareFeatureNotFoundException(
                sprintf(
                    SoftwareFeatureNotFoundException::CODE_NOT_FOUND,
                    $code
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }
}