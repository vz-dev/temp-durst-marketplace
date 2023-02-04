<?php
/**
 * Durst - project - Merchant.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:55
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\Merchant\Business\Exception\MerchantExistsException;
use Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Pyz\Zed\Merchant\MerchantConfig;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Merchant implements MerchantInterface
{
    //TODO Refactor
    public const MERCHANT_BUNDLE_SESSION_KEY = 'merchant';
    public const KEY_CURRENT_MERCHANT = 'currentMerchant';

    private const KEY_PASSWORD_ALGO = 'algo';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Pyz\Zed\Merchant\MerchantConfig
     */
    protected $settings;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\MerchantHydratorPluginInterface[]
     */
    protected $hydratorPlugins;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\MerchantSaverPluginInterface[]
     */
    protected $saverPlugins;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Pyz\Zed\Merchant\MerchantConfig $settings
     * @param array $hydratorPlugins
     * @param array $saverPlugins
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer,
        SessionInterface                $session,
        MerchantConfig                  $settings,
        array                           $hydratorPlugins,
        array                           $saverPlugins
    )
    {
        $this->queryContainer = $queryContainer;
        $this->session = $session;
        $this->settings = $settings;
        $this->hydratorPlugins = $hydratorPlugins;
        $this->saverPlugins = $saverPlugins;
    }


    /**
     * @param string $password
     *
     * @return string
     */
    public function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @param MerchantTransfer $merchantTransfer
     * @return MerchantTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     */
    public function save(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        if ($merchantTransfer->getIdMerchant() !== null) {
            $merchantEntity = $this
            ->queryContainer
            ->queryMerchantById($merchantTransfer->getIdMerchant())
            ->findOneOrCreate();
        } else {
            $merchantEntity = new SpyMerchant();
        }

        $this->hydrateEntity($merchantEntity, $merchantTransfer);
        $this->checkUnique($merchantEntity);

        if($merchantEntity->isNew() || $merchantEntity->isModified()){
            $merchantEntity->save();
        }

        return $this->entityToTransfer($merchantEntity);
    }

    /**
     * @param int $idMerchant
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException save() throws this if an id is set in the merchant
     * transfer that cannot be found in the database. As no id will be set this can't really
     * happen
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     */
    public function removeMerchant(int $idMerchant): MerchantTransfer
    {
        $merchant = $this->getMerchantById($idMerchant);
        $merchant->setStatus(SpyMerchantTableMap::COL_STATUS_DELETED);

        return $this->save($merchant);
    }

    /**
     * @param string $merchantname
     * @return bool
     */
    public function hasMerchantByMerchantname(string $merchantname): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantByMerchantname($merchantname)
            ->count();

        return $amount > 0;
    }

    /**
     * @param string $merchantPin
     * @return bool
     */
    public function hasMerchantByMerchantPin(string $merchantPin): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantByMerchantPin($merchantPin)
            ->count();

        return $amount > 0;
    }

    /**
     * @param string $merchantname
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasActiveMerchantByMerchantname(string $merchantname): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantByMerchantname($merchantname)
            ->filterByStatus(SpyMerchantTableMap::COL_STATUS_ACTIVE)
            ->count();

        return $amount > 0;
    }

    /**
     * @param int $idMerchant
     * @return bool
     */
    public function hasMerchantById(int $idMerchant): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantById($idMerchant)
            ->count();

        return $amount > 0;
    }

    /**
     * @param string $merchantname
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getMerchantByMerchantname(string $merchantname): MerchantTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryMerchantByMerchantname($merchantname)
            ->findOne();

        if ($entity === null) {
            throw new MerchantNotFoundException();
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer
    {
        $entity = $this->queryContainer
            ->queryMerchantById($idMerchant)
            ->findOne();

        if ($entity === null) {
            throw new MerchantNotFoundException(
                sprintf(
                    MerchantNotFoundException::MESSAGE_ID,
                    $idMerchant
                )
            );
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param string $merchantPin
     *
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryMerchant()
            ->filterByStatus(
                SpyMerchantTableMap::COL_STATUS_ACTIVE
            )
            ->filterByMerchantPin($merchantPin)
            ->findOne();

        if ($entity === null) {
            throw new MerchantNotFoundException(
                sprintf(
                    MerchantNotFoundException::MESSAGE_PIN,
                    $merchantPin
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param int $idMerchant
     * @param bool $hasBranchUser
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException if not merchant with the given id can be found
     * in the database
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveMerchantById(int $idMerchant, bool $hasBranchUser = false): MerchantTransfer
    {
        $query = $this
            ->queryContainer
            ->queryMerchantById($idMerchant);

        if ($hasBranchUser !== true) {
            $query = $query
                ->filterByStatus(SpyMerchantTableMap::COL_STATUS_ACTIVE);
        }

        $entity = $query
            ->findOne();

        if ($entity === null) {
            throw new MerchantNotFoundException(
                sprintf(
                    MerchantNotFoundException::MESSAGE_ID_ACTIVE,
                    $idMerchant
                )
            );
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $idMerchant
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function activateMerchant(int $idMerchant): bool
    {
        $merchantEntity = $this
            ->queryContainer
            ->queryMerchantById($idMerchant)
            ->findOne();

        $merchantEntity->setStatus(SpyMerchantTableMap::COL_STATUS_ACTIVE);
        $rowsAffected = $merchantEntity->save();

        return $rowsAffected > 0;
    }

    /**
     * @param int $idMerchant
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function deactivateMerchant(int $idMerchant): bool
    {
        $merchantEntity = $this
            ->queryContainer
            ->queryMerchantById($idMerchant)
            ->findOne();

        $merchantEntity->setStatus(SpyMerchantTableMap::COL_STATUS_BLOCKED);
        $rowsAffected = $merchantEntity->save();

        return $rowsAffected > 0;
    }

    /**
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getCurrentMerchant(): MerchantTransfer
    {
        $merchant = $this->readMerchantFromSession();

        if ($merchant === null) {
            throw new MerchantNotFoundException(
                MerchantNotFoundException::MESSAGE_NO_IN_SESSION
            );
        }

        return clone $merchant;
    }

    /**
     * @return bool
     */
    public function hasCurrentMerchant() : bool
    {
        $merchant = $this->readMerchantFromSession();

        return $merchant !== null;
    }

    /**
     * @param MerchantTransfer $merchant
     * @return mixed
     */
    public function setCurrentMerchant(MerchantTransfer $merchant)
    {
        $key = $this->createMerchantKey();

        return $this->session->set($key, clone $merchant);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentMerchant(): void
    {
        $key = $this
            ->createMerchantKey();

        if ($this->session->has($key)) {
            $this
                ->session
                ->remove($key);

            $this
                ->session
                ->migrate();
        }
    }

    /**
     * @return MerchantTransfer[]
     */
    public function getMerchants(): array
    {
        $merchantEntities =  $this
            ->queryContainer
            ->queryMerchant()
            ->find();

        $merchantTransfers = [];
        foreach($merchantEntities as $merchantEntity){
            $merchantTransfers[] = $this->entityToTransfer($merchantEntity);
        }

        return $merchantTransfers;
    }

    /**
     * @param string $merchantPin
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasActiveMerchantByMerchantPin(string $merchantPin): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantByMerchantPin($merchantPin)
            ->filterByStatus(SpyMerchantTableMap::COL_STATUS_ACTIVE)
            ->count();

        return $amount > 0;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getMerchantByIdBranch(int $idBranch): MerchantTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryMerchantByIdBranch($idBranch)
            ->findOne();

        if ($entity === null) {
            throw new MerchantNotFoundException(
                sprintf(
                    MerchantNotFoundException::MESSAGE_ID_BRANCH,
                    $idBranch
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @return MerchantTransfer
     */
    protected function entityToTransfer(SpyMerchant $merchantEntity): MerchantTransfer
    {
        $merchantTransfer = new MerchantTransfer();
        $merchantTransfer->fromArray($merchantEntity->toArray(), true);

        $this->hydrateMerchant($merchantEntity, $merchantTransfer);

        return $merchantTransfer;
    }

    /**
     * @param SpyMerchant $entity
     * @param MerchantTransfer $transfer
     * @return void
     */
    protected function hydrateMerchant(
        SpyMerchant $entity,
        MerchantTransfer $transfer
    ): void
    {
        foreach ($this->hydratorPlugins as $hydratorPlugin) {
            $hydratorPlugin->hydrateMerchant($entity, $transfer);
        }
    }

    /**
     * @return MerchantTransfer|null
     */
    protected function readMerchantFromSession(): ?MerchantTransfer
    {
        $key = $this->createMerchantKey();

        if (!$this->session->has($key)) {
            return null;
        }

        return $this->session->get($key);
    }

    /**
     * @return string
     */
    protected function createMerchantKey(): string
    {
        return sprintf(
            '%s:%s',
            static::MERCHANT_BUNDLE_SESSION_KEY,
            static::KEY_CURRENT_MERCHANT
        );
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     */
    protected function checkUnique(SpyMerchant $merchantEntity): void
    {
        if($merchantEntity->isNew()){
            if($this->hasMerchantByMerchantname($merchantEntity->getMerchantname()) === true){
                throw new MerchantExistsException(
                    sprintf(
                        MerchantExistsException::MESSAGE,
                        $merchantEntity->getMerchantname()
                    )
                );
            }

            if ($this->hasMerchantByMerchantPin($merchantEntity->getMerchantPin()) === true) {
                throw new MerchantExistsException(
                    sprintf(
                        MerchantExistsException::MESSAGE_PIN,
                        $merchantEntity->getMerchantPin()
                    )
                );
            }
        }
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hydrateEntity(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    ): void
    {
        $merchantEntity->setFirstName($merchantTransfer->getFirstName());
        $merchantEntity->setLastName($merchantTransfer->getLastName());
        $merchantEntity->setMerchantname($merchantTransfer->getMerchantname());
        $merchantEntity->setCompany($merchantTransfer->getCompany());
        $merchantEntity->setSalutation($merchantTransfer->getSalutation());
        $merchantEntity->setMerchantPin($merchantTransfer->getMerchantPin());
        $merchantEntity->setFkAclGroup($merchantTransfer->getFkAclGroup());

        if($merchantTransfer->getStatus() !== null) {
            $merchantEntity->setStatus($merchantTransfer->getStatus());
        }

        if ($merchantTransfer->getLastLogin() !== null) {
            $merchantEntity->setLastLogin($merchantTransfer->getLastLogin());
        }

        $password = $merchantTransfer->getPassword();
        if (!empty($password) && $this->isRawPassword($password)) {
            $merchantEntity->setPassword($this->encryptPassword($password));
        }

        $this->runSaverPlugins($merchantEntity, $merchantTransfer);
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     */
    protected function runSaverPlugins(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    ): void
    {
        foreach ($this->saverPlugins as $saverPlugin) {
            $saverPlugin->saveMerchant($merchantEntity, $merchantTransfer);
        }
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    private function isRawPassword(string $password) : bool
    {
        $passwordInfo = password_get_info($password);

        return $passwordInfo[self::KEY_PASSWORD_ALGO] === 0;
    }
}
