<?php
/**
 * Durst - project - IntegraCredentials.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 10:20
 */

namespace Pyz\Zed\Integra\Business\Model;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Orm\Zed\Integra\Persistence\Map\PyzIntegraCredentialsTableMap;
use Orm\Zed\Integra\Persistence\PyzIntegraCredentials;
use Pyz\Zed\Integra\Business\Exception\EntityNotFoundException;
use Pyz\Zed\Integra\Business\Model\Encryption\PasswordManagerInterface;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class IntegraCredentials implements IntegraCredentialsInterface
{
    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var PasswordManagerInterface
     */
    protected $passwordManager;

    /**
     * IntegraCredentials constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     * @param PasswordManagerInterface $passwordManager
     */
    public function __construct(
        IntegraQueryContainerInterface $queryContainer,
        PasswordManagerInterface $passwordManager
    ) {
        $this->queryContainer = $queryContainer;
        $this->passwordManager = $passwordManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param IntegraCredentialsTransfer $transfer
     *
     * @return void
     */
    public function save(IntegraCredentialsTransfer $transfer): void
    {
        $entity = $this->getEntity($transfer->getIdIntegraCredentials());
        $ftpPassword = $entity->getFtpPassword();
        $soapPassword = $entity->getSoapAuthPassword();
        $entity->fromArray($transfer->toArray());
        $entity->setFtpPassword($ftpPassword);
        if ($transfer->getFtpPassword() !== null) {
            $entity->setFtpPassword($this->passwordManager->encryptPassword($transfer->getFtpPassword()));
        }
        $entity->setSoapAuthPassword($soapPassword);
        if($transfer->getSoapAuthPassword() !== null) {
            $entity->setSoapAuthPassword($this->passwordManager->encryptPassword($transfer->getSoapAuthPassword()));
        }

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }

    /**
     * @param int $idCredentialsTransfer
     *
     * @return void
     */
    public function remove(int $idCredentialsTransfer): void
    {
        $entity = $this
            ->queryContainer
            ->queryIntegraCredentialsById($idCredentialsTransfer)
            ->findOne();

        if ($entity !== null) {
            $entity->delete();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getBranchIdsThatUseIntegra(): array
    {
        return $this
            ->queryContainer
            ->queryActiveIntegraCredentials()
            ->select(PyzIntegraCredentialsTableMap::COL_FK_BRANCH)
            ->find()
            ->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool
    {
        return ($this
            ->queryContainer
            ->queryIntegraCredentialsByIdBranch($idBranch)
            ->filterByUseIntegra(true)
            ->count() > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryIntegraCredentialsByIdBranch($idBranch)
            ->findOne();

        if ($entity === null) {
            EntityNotFoundException::branch($idBranch);
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param PyzIntegraCredentials $entity
     *
     * @return IntegraCredentialsTransfer
     */
    protected function entityToTransfer(PyzIntegraCredentials $entity): IntegraCredentialsTransfer
    {
        $transfer = (new IntegraCredentialsTransfer())
            ->fromArray($entity->toArray(), true);

        $transfer->setFtpPassword($this->passwordManager->decryptPassword($entity->getFtpPassword()));
        $transfer->setSoapAuthPassword($this->passwordManager->decryptPassword($entity->getSoapAuthPassword()));

        return $transfer;
    }

    /**
     * @param int|null $idIntegraCredentials
     *
     * @return PyzIntegraCredentials
     */
    protected function getEntity(?int $idIntegraCredentials = null): PyzIntegraCredentials
    {
        if ($idIntegraCredentials === null) {
            return new PyzIntegraCredentials();
        }

        $entity = $this
            ->queryContainer
            ->queryIntegraCredentialsById($idIntegraCredentials)
            ->findOne();

        if ($entity === null) {
            throw EntityNotFoundException::build($idIntegraCredentials);
        }

        return $entity;
    }
}
