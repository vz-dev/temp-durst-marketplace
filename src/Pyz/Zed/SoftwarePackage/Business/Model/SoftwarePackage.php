<?php
/**
 * Durst - project - SoftwarePackage.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:32
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;

use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;
use Orm\Zed\Sales\Persistence\Map\DstSoftwarePackageTableMap;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Pyz\Zed\SoftwarePackage\Business\Exception\InvalidStatusException;
use Pyz\Zed\SoftwarePackage\Business\Exception\SoftwarePackageNotFoundException;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\PaymentMethodHydratorInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\SoftwareFeatureHydratorInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\PaymentMethodSaverInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\SoftwareFeatureSaverInterface;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class SoftwarePackage implements SoftwarePackageInterface
{
    /**
     * @var \Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\PaymentMethodHydratorInterface
     */
    protected $paymentMethodHydrator;

    /**
     * @var \Pyz\Zed\SoftwarePackage\Business\Model\Saver\PaymentMethodSaverInterface
     */
    protected $paymentMethodSaver;

    /**
     * @var \Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\SoftwareFeatureHydratorInterface
     */
    protected $softwareFeatureHydrator;

    /**
     * @var \Pyz\Zed\SoftwarePackage\Business\Model\Saver\SoftwareFeatureSaverInterface
     */
    protected $softwareFeatureSaver;

    /**
     * SoftwarePackage constructor.
     *
     * @param \Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\PaymentMethodHydratorInterface $paymentMethodHydrator
     * @param \Pyz\Zed\SoftwarePackage\Business\Model\Saver\PaymentMethodSaverInterface $paymentMethodSaver
     * @param \Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\SoftwareFeatureHydratorInterface $softwareFeatureHydrator
     * @param \Pyz\Zed\SoftwarePackage\Business\Model\Saver\SoftwareFeatureSaverInterface $softwareFeatureSaver
     */
    public function __construct(
        SoftwarePackageQueryContainerInterface $queryContainer,
        PaymentMethodHydratorInterface $paymentMethodHydrator,
        PaymentMethodSaverInterface $paymentMethodSaver,
        SoftwareFeatureHydratorInterface $softwareFeatureHydrator,
        SoftwareFeatureSaverInterface $softwareFeatureSaver
    ) {
        $this->queryContainer = $queryContainer;
        $this->paymentMethodHydrator = $paymentMethodHydrator;
        $this->paymentMethodSaver = $paymentMethodSaver;
        $this->softwareFeatureHydrator = $softwareFeatureHydrator;
        $this->softwareFeatureSaver = $softwareFeatureSaver;
    }

    /**
     * @param string $code
     *
     * @throws \Pyz\Zed\SoftwarePackage\Business\Exception\SoftwarePackageNotFoundException
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageByCode(string $code): SoftwarePackageTransfer
    {
        $softwarePackageEntity = $this
            ->queryContainer
            ->querySoftwarePackageByCode($code)
            ->findOne();

        if ($softwarePackageEntity === null) {
            throw new SoftwarePackageNotFoundException(
                sprintf(
                    SoftwarePackageNotFoundException::MESSAGE,
                    $code
                )
            );
        }

        return $this
            ->entityToTransfer($softwarePackageEntity);
    }

    /**
     * @param int $idSoftwarePackage
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function getSoftwarePackageById(int $idSoftwarePackage): SoftwarePackageTransfer
    {
        $softwarePackageEntity = $this
            ->getSoftwarePackageEntityById($idSoftwarePackage);

        return $this
            ->entityToTransfer($softwarePackageEntity);
    }

    /**
     * @param int $idSoftwarePackage
     *
     * @throws \Pyz\Zed\SoftwarePackage\Business\Exception\SoftwarePackageNotFoundException
     *
     * @return \Orm\Zed\Sales\Persistence\DstSoftwarePackage
     */
    protected function getSoftwarePackageEntityById(int $idSoftwarePackage): DstSoftwarePackage
    {
        $softwarePackageEntity = $this
            ->queryContainer
            ->querySoftwarePackageById($idSoftwarePackage)
            ->findOne();

        if ($softwarePackageEntity === null) {
            throw new SoftwarePackageNotFoundException(
                sprintf(
                    SoftwarePackageNotFoundException::MESSAGE_ID,
                    $idSoftwarePackage
                )
            );
        }

        return $softwarePackageEntity;
    }

    /**
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer[]
     */
    public function getSoftwarePackages(): array
    {
        $entities = $this
            ->queryContainer
            ->querySoftwarePackage()
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
     * @param int $idMerchant
     *
     * @return bool
     *
     * @throws AmbiguousComparisonException
     */
    public function hasMerchantWholesalePackage(int $idMerchant): bool
    {
        $softwarePackageEntity = $this->getSoftwarePackageByMerchantId($idMerchant);

        return ($softwarePackageEntity !== null &&
            $softwarePackageEntity->getCode() === SoftwarePackageConstants::SOFTWARE_PACKAGE_WHOLESALE_CODE);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     *
     * @return bool
     *
     * @throws AmbiguousComparisonException
     */
    public function hasMerchantRetailPackage(int $idMerchant): bool
    {
        $softwarePackageEntity = $this->getSoftwarePackageByMerchantId($idMerchant);

        return ($softwarePackageEntity !== null &&
            $softwarePackageEntity->getCode() === SoftwarePackageConstants::SOFTWARE_PACKAGE_RETAIL_CODE);
    }

    /**
     * @param \Generated\Shared\Transfer\SoftwarePackageTransfer $softwarePackageTransfer
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    public function saveSoftwarePackage(SoftwarePackageTransfer $softwarePackageTransfer): SoftwarePackageTransfer
    {
        $entity = $this
            ->queryContainer
            ->querySoftwarePackageByCode($softwarePackageTransfer->getCode())
            ->findOneOrCreate();

        $entity->fromArray($softwarePackageTransfer->toArray());

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }

        $softwarePackageTransfer->setIdSoftwarePackage($entity->getIdSoftwarePackage());

        $this
            ->paymentMethodSaver
            ->savePaymentMethodsForSoftwarePackage($softwarePackageTransfer);

        $this
            ->softwareFeatureSaver
            ->saveSoftwareFeaturesForSoftwarePackage($softwarePackageTransfer);

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param int $idSoftwarePackage
     * @param string $status
     *
     * @throws \Pyz\Zed\SoftwarePackage\Business\Exception\InvalidStatusException
     *
     * @return void
     */
    protected function setStatusForSoftwarePackage(int $idSoftwarePackage, string $status)
    {
        if ($status !== DstSoftwarePackageTableMap::COL_STATUS_ACTIVE &&
            $status !== DstSoftwarePackageTableMap::COL_STATUS_INACTIVE &&
            $status !== DstSoftwarePackageTableMap::COL_STATUS_DELETED) {
            throw new InvalidStatusException(
                sprintf(
                    InvalidStatusException::MESSAGE,
                    $status
                )
            );
        }

        $entity = $this
            ->getSoftwarePackageEntityById($idSoftwarePackage);

        $entity->setStatus($status);

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function activateSoftwarePackage(int $idSoftwarePackage)
    {
        $this->setStatusForSoftwarePackage(
            $idSoftwarePackage,
            DstSoftwarePackageTableMap::COL_STATUS_ACTIVE
        );
    }

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function deactivateSoftwarePackage(int $idSoftwarePackage)
    {
        $this->setStatusForSoftwarePackage(
            $idSoftwarePackage,
            DstSoftwarePackageTableMap::COL_STATUS_INACTIVE
        );
    }

    /**
     * @param int $idSoftwarePackage
     *
     * @return void
     */
    public function deleteSoftwarePackage(int $idSoftwarePackage)
    {
        $this->setStatusForSoftwarePackage(
            $idSoftwarePackage,
            DstSoftwarePackageTableMap::COL_STATUS_DELETED
        );
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\DstSoftwarePackage $entity
     *
     * @return \Generated\Shared\Transfer\SoftwarePackageTransfer
     */
    protected function entityToTransfer(DstSoftwarePackage $entity): SoftwarePackageTransfer
    {
        $transfer = (new SoftwarePackageTransfer())
            ->fromArray($entity->toArray(), true);

        $this
            ->paymentMethodHydrator
            ->hydrateSoftwarePackageByPaymentMethods($entity, $transfer);

        $this
            ->softwareFeatureHydrator
            ->hydrateSoftwarePackageBySoftwareFeatures($entity, $transfer);

        return $transfer;
    }

    /**
     * @param int $idMerchant
     *
     * @return DstSoftwarePackage|null
     *
     * @throws AmbiguousComparisonException
     */
    protected function getSoftwarePackageByMerchantId(int $idMerchant): ?DstSoftwarePackage
    {
        return $this
            ->queryContainer
            ->querySoftwarePackage()
            ->useSpyMerchantQuery()
                ->filterByIdMerchant($idMerchant)
            ->endUse()
            ->findOne();
    }
}
