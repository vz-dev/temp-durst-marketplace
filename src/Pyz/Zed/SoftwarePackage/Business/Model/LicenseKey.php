<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-08
 * Time: 12:02
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model;

use Generated\Shared\Transfer\LicenseTransfer;
use Orm\Zed\Sales\Persistence\DstLicense;
use Orm\Zed\Sales\Persistence\Map\DstLicenseTableMap;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\SoftwarePackageConfig;

/**
 * Class LicenseKey
 * @package Pyz\Zed\SoftwarePackage\Business\Model
 */
class LicenseKey implements LicenseKeyInterface
{
    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var SoftwarePackageConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * LicenseKey constructor.
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     * @param SoftwarePackageConfig $config
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(SoftwarePackageQueryContainerInterface $queryContainer, SoftwarePackageConfig $config, MerchantFacadeInterface $merchantFacade)
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return LicenseTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getLicenseKeysByIdBranch(int $idBranch): array
    {
        $entities = $this
            ->queryContainer
            ->queryLicenseKeysByIdBranch($idBranch)
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
     * @param int $idBranch
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getLicenseUnitsCountByIdBranch(int $idBranch): int
    {
        $transfers = $this
            ->getLicenseKeysByIdBranch($idBranch);

        $count = 0;

        foreach ($transfers as $transfer) {
            $count += $transfer
                ->getUnits();
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @return bool
     */
    public function validateLicenseKeyCode(string $code, int $idSoftwarePackage): bool
    {
        $license = $this
            ->getLicenseByCodeAndSoftwarePackage($code, $idSoftwarePackage);

        return ($license->getIdLicense() !== null);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @param int $idSoftwarePackage
     * @param int $idBranch
     * @return LicenseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function redeemLicenseKeyCode(string $code, int $idSoftwarePackage, int $idBranch): LicenseTransfer
    {
        $validCode = $this
            ->validateLicenseKeyCode($code, $idSoftwarePackage);

        if ($validCode !== true) {
            return new LicenseTransfer();
        }

        $currentBranch = $this
            ->merchantFacade
            ->getBranchById($idBranch);

        $initialUnits = 0;

        if ($currentBranch->getUnitsLicenseCount() === null || $currentBranch->getUnitsLicenseCount() === 0) {
            $initialUnits = $this
                ->getLicenseUnitsCountByIdBranch($idBranch);
        }

        $licenseTransfer = $this
            ->getLicenseByCodeAndSoftwarePackage($code, $idSoftwarePackage);

        $licenseUnits = $initialUnits + $licenseTransfer->getUnits();

        $this
            ->merchantFacade
            ->sumUpLicenseUnitsToBranchById($idBranch, $licenseUnits);

        $licenseTransfer
            ->setFkBranch($idBranch)
            ->setStatus(DstLicenseTableMap::COL_STATUS_REDEEMED)
            ->setRedeemedAt(new \DateTime('now'));

        return $this
            ->save($licenseTransfer);
    }

    /**
     * @param string $code
     * @param int $idSoftwarePackage
     * @return LicenseTransfer
     */
    protected function getLicenseByCodeAndSoftwarePackage(string $code, int $idSoftwarePackage): LicenseTransfer
    {
        $licenseKey = $this
            ->encryptLicenseKey($code);

        $license = $this
            ->queryContainer
            ->queryLicenseKeyCode($licenseKey, $idSoftwarePackage)
            ->findOne();

        if ($license === null || $license->getIdLicense() === null) {
            return new LicenseTransfer();
        }

        return $this
            ->entityToRawTransfer($license);
    }

    /**
     * @param DstLicense $entity
     * @return LicenseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function entityToTransfer(DstLicense $entity): LicenseTransfer
    {
        $transfer = new LicenseTransfer();

        $transfer
            ->fromArray($entity->toArray());

        if ($entity->getRedeemedAt() !== null) {
            $transfer
                ->setRedeemedAt($this->addProjectTimezoneToDateTime($entity->getRedeemedAt()));
        }

        $transfer
            ->setLicenseKey($this->decryptLicenseKey($entity->getLicenseKey()));

        return $transfer;
    }

    /**
     * @param DstLicense $license
     * @return LicenseTransfer
     */
    protected function entityToRawTransfer(DstLicense $license): LicenseTransfer
    {
        return (new LicenseTransfer())
            ->fromArray(
                $license->toArray()
            );
    }

    /**
     * @param LicenseTransfer $licenseTransfer
     * @return LicenseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function save(LicenseTransfer $licenseTransfer): LicenseTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryLicenseKeyById($licenseTransfer->getIdLicense())
            ->findOneOrCreate();

        $entity
            ->fromArray($licenseTransfer->toArray());

        if ($entity->isModified() === true) {
            $entity
                ->save();
        }

        return $this
            ->entityToRawTransfer($entity);
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    protected function addProjectTimezoneToDateTime(\DateTime $date): \DateTime
    {
        $projectTimezone = new \DateTimeZone($this->config->getProjectTimeZone());

        $date
            ->setTimezone($projectTimezone);

        return $date;
    }

    /**
     * @param string $unencryptedCode
     * @return string
     */
    protected function encryptLicenseKey(string $unencryptedCode): string
    {
        $key = hash(
            'sha256',
            $this->config->getLicenseKeyKey()
        );

        $iv = substr(
            hash(
                'sha256',
                $this->config->getLicenseKeyIV()
            ),
            0,
            16
        );

        $output = openssl_encrypt(
            $unencryptedCode,
            $this->config->getLicenseKeyMethod(),
            $key,
            0,
            $iv
        );

        $encryptedCode = base64_encode($output);;

        return $encryptedCode;
    }

    /**
     * @param string $encryptedCode
     * @return string
     */
    protected function decryptLicenseKey(string $encryptedCode): string
    {
        $key = hash(
            'sha256',
            $this->config->getLicenseKeyKey()
        );

        $iv = substr(
            hash(
                'sha256',
                $this->config->getLicenseKeyIV()
            ),
            0,
            16
        );

        $unencryptedCode = openssl_decrypt(
            base64_decode($encryptedCode),
            $this->config->getLicenseKeyMethod(),
            $key,
            0,
            $iv
        );

        return $unencryptedCode;
    }
}
