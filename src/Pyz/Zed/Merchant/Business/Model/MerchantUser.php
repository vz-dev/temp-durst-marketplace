<?php
/**
 * Durst - project - MerchantUser.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.21
 * Time: 10:54
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantUserTransfer;
use Orm\Zed\Merchant\Persistence\DstMerchantUser;
use Orm\Zed\Merchant\Persistence\Map\DstMerchantUserTableMap;
use Pyz\Zed\Merchant\Business\Exception\MerchantUserEmailNotUniqueException;
use Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException;
use Pyz\Zed\Merchant\MerchantConfig;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MerchantUser implements MerchantUserInterface
{
    public const MERCHANT_USER_SESSION_KEY = 'merchantuser';
    public const KEY_CURRENT_MERCHANT_USER = 'currentMerchantUser';

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
     * @var array|\Pyz\Zed\Merchant\Communication\Plugin\MerchantUserHydratorPluginInterface[]
     */
    protected $hydratorPlugins;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Pyz\Zed\Merchant\MerchantConfig $settings
     * @param array $hydratorPlugins
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer,
        SessionInterface                $session,
        MerchantConfig                  $settings,
        array                           $hydratorPlugins
    )
    {
        $this->queryContainer = $queryContainer;
        $this->session = $session;
        $this->settings = $settings;
        $this->hydratorPlugins = $hydratorPlugins;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getMerchantUserById(int $idMerchantUser): MerchantUserTransfer
    {
        $merchantUser = $this
            ->queryContainer
            ->queryMerchantUserById(
                $idMerchantUser
            )
            ->filterByStatus(
                DstMerchantUserTableMap::COL_STATUS_ACTIVE
            )
            ->findOne();

        if ($merchantUser->getIdMerchantUser() === null) {
            throw new MerchantUserNotFoundException(
                sprintf(
                    MerchantUserNotFoundException::MESSAGE,
                    $idMerchantUser
                )
            );
        }

        return $this
            ->entityToTransfer(
                $merchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getMerchantUserByEmail(string $email): MerchantUserTransfer
    {
        $merchantUser = $this
            ->queryContainer
            ->queryMerchantUserByEmail(
                $email
            )
            ->filterByStatus(
                DstMerchantUserTableMap::COL_STATUS_ACTIVE
            )
            ->findOne();

        if ($merchantUser->getIdMerchantUser() === null) {
            throw new MerchantUserNotFoundException(
                sprintf(
                    MerchantUserNotFoundException::MESSAGE_EMAIL,
                    $email
                )
            );
        }

        return $this
            ->entityToTransfer(
                $merchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserEmailNotUniqueException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(MerchantUserTransfer $merchantUserTransfer): MerchantUserTransfer
    {
        $merchanthUserEntity = $this
            ->queryContainer
            ->queryMerchantUserById(
                $merchantUserTransfer
                    ->getIdMerchantUser()
            )
            ->findOneOrCreate();

        $this
            ->hydrateEntity(
                $merchanthUserEntity,
                $merchantUserTransfer
            );

        $this
            ->checkUnique(
                $merchanthUserEntity
            );

        if (
            $merchanthUserEntity->isNew() === true ||
            $merchanthUserEntity->isModified() === true
        ) {
            $merchanthUserEntity
                ->save();
        }

        return $this
            ->entityToTransfer(
                $merchanthUserEntity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function deleteMerchantUser(int $idMerchantUser): bool
    {
        $merchantUser = $this
            ->queryContainer
            ->queryMerchantUserById(
                $idMerchantUser
            )
            ->findOne();

        if (
            $merchantUser === null ||
            $merchantUser->getIdMerchantUser() === null
        ) {
            throw new MerchantUserNotFoundException(
                sprintf(
                    MerchantUserNotFoundException::MESSAGE,
                    $idMerchantUser
                )
            );
        }

        $merchantUser
            ->setStatus(DstMerchantUserTableMap::COL_STATUS_DELETED);

        $rowsAffected = $merchantUser
            ->save();

        return ($rowsAffected > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function activateMerchantUser(int $idMerchantUser): bool
    {
        $merchantUser = $this
            ->queryContainer
            ->queryMerchantUserById(
                $idMerchantUser
            )
            ->findOne();

        if (
            $merchantUser === null ||
            $merchantUser->getIdMerchantUser() === null
        ) {
            throw new MerchantUserNotFoundException(
                sprintf(
                    MerchantUserNotFoundException::MESSAGE,
                    $idMerchantUser
                )
            );
        }

        $merchantUser
            ->setStatus(DstMerchantUserTableMap::COL_STATUS_ACTIVE);

        $rowsAffected = $merchantUser
            ->save();

        return ($rowsAffected > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function deactivateMerchantUser(int $idMerchantUser): bool
    {
        $merchantUser = $this
            ->queryContainer
            ->queryMerchantUserById(
                $idMerchantUser
            )
            ->findOne();

        if (
            $merchantUser === null ||
            $merchantUser->getIdMerchantUser() === null
        ) {
            throw new MerchantUserNotFoundException(
                sprintf(
                    MerchantUserNotFoundException::MESSAGE,
                    $idMerchantUser
                )
            );
        }

        $merchantUser
            ->setStatus(DstMerchantUserTableMap::COL_STATUS_BLOCKED);

        $rowsAffected = $merchantUser
            ->save();

        return ($rowsAffected > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function getCurrentMerchantUser(): MerchantUserTransfer
    {
        $merchantUser = $this
            ->readMerchantUserFromSession();

        if ($merchantUser === null) {
            throw new MerchantUserNotFoundException(
                MerchantUserNotFoundException::MESSAGE_NOT_IN_SESSION
            );
        }

        return clone $merchantUser;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return mixed
     */
    public function setCurrentMerchantUser(MerchantUserTransfer $merchantUserTransfer)
    {
        $key = $this
            ->createMerchantUserKey();

        return $this
            ->session
            ->set(
                $key,
                clone $merchantUserTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool
    {
        return password_verify(
            $password,
            $hash
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function logout(): void
    {
        $key = $this
            ->createMerchantUserKey();

        if (
            $this->session->has($key) !== false
        ) {
            $this
                ->session
                ->remove(
                    $key
                );

            $this
                ->session
                ->migrate();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return bool
     */
    public function hasActiveMerchantUserByEmail(string $email): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantUserByEmail(
                $email
            )
            ->filterByStatus(
                DstMerchantUserTableMap::COL_STATUS_ACTIVE
            )
            ->count();

        return ($amount > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function hasCurrentMerchantUser(): bool
    {
        $merchantUser = $this
            ->readMerchantUserFromSession();

        return ($merchantUser !== null);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentMerchantUser(): void
    {
        $key = $this
            ->createMerchantUserKey();

        if (
            $this->session->has($key)
        ) {
            $this
                ->session
                ->remove(
                    $key
                );

            $this
                ->session
                ->migrate();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return array|MerchantUserTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getMerchantUsersByIdMerchant(int $idMerchant): array
    {
        $merchantUserEntities = $this
            ->queryContainer
            ->queryMerchantUser()
            ->filterByFkMerchant(
                $idMerchant
            )
            ->find();

        $merchantUsers = [];

        foreach ($merchantUserEntities as $merchantUserEntity) {
            $merchantUsers[] = $this
                ->entityToTransfer(
                    $merchantUserEntity
                );
        }

        return $merchantUsers;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    protected function entityToTransfer(DstMerchantUser $merchantUser): MerchantUserTransfer
    {
        $merchantUserTransfer = new MerchantUserTransfer();
        $merchantUserTransfer
            ->fromArray(
                $merchantUser->toArray(),
                true
            );

        $this
            ->hydrateMerchantUser(
                $merchantUser,
                $merchantUserTransfer
            );

        return $merchantUserTransfer;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return void
     */
    protected function hydrateMerchantUser(
        DstMerchantUser $merchantUser,
        MerchantUserTransfer $merchantUserTransfer
    ): void
    {
        foreach ($this->hydratorPlugins as $hydratorPlugin) {
            $hydratorPlugin
                ->hydrateMerchantUser(
                    $merchantUser,
                    $merchantUserTransfer
                );
        }
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserEmailNotUniqueException
     */
    protected function checkUnique(DstMerchantUser $merchantUser): void
    {
        if ($merchantUser->isNew() === true) {
            if (
                $this->hasMerchantUserByEmail($merchantUser->getEmail()) === true ||
                $this->hasBranchUserByEmail($merchantUser->getEmail()) === true ||
                $this->hasMerchantByEmail($merchantUser->getEmail()) === true
            ) {
                throw new MerchantUserEmailNotUniqueException(
                    sprintf(
                        MerchantUserEmailNotUniqueException::MESSAGE,
                        $merchantUser->getEmail()
                    )
                );
            }
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    protected function hasMerchantUserByEmail(string $email): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantUserByEmail($email)
            ->count();

        return ($amount > 0);
    }

    /**
     * @param string $email
     * @return bool
     */
    protected function hasBranchUserByEmail(string $email): bool
    {
        $amount = $this
            ->queryContainer
            ->queryBranchUserByEmail($email)
            ->count();

        return ($amount > 0);
    }

    /**
     * @param string $email
     * @return bool
     */
    protected function hasMerchantByEmail(string $email): bool
    {
        $amount = $this
            ->queryContainer
            ->queryMerchantByEmail($email)
            ->count();

        return ($amount > 0);
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hydrateEntity(
        DstMerchantUser $merchantUser,
        MerchantUserTransfer $merchantUserTransfer
    ): void
    {
        $merchantUser
            ->setSalutation($merchantUserTransfer->getSalutation())
            ->setFirstName($merchantUserTransfer->getFirstName())
            ->setLastName($merchantUserTransfer->getLastName())
            ->setEmail($merchantUserTransfer->getEmail())
            ->setFkMerchant($merchantUserTransfer->getFkMerchant())
            ->setFkAclGroup($merchantUserTransfer->getFkAclGroup());

        $password = $merchantUserTransfer
            ->getPassword();

        if (
            !empty($password) &&
            $this->isRawPassword($password)
        ) {
            $merchantUser
                ->setPassword(
                    $this->encryptPassword($password)
                );
        }

        if ($merchantUserTransfer->getStatus() !== null) {
            $merchantUser
                ->setStatus(
                    $merchantUserTransfer->getStatus()
                );
        } else {
            $merchantUser
                ->setStatus(
                    DstMerchantUserTableMap::COL_STATUS_BLOCKED
                );
        }

        if ($merchantUserTransfer->getLastLogin() !== null) {
            $merchantUser
                ->setLastLogin(
                    $merchantUserTransfer->getLastLogin()
                );
        }
    }

    /**
     * @param string $password
     * @return string
     */
    protected function encryptPassword(string $password): string
    {
        return password_hash(
            $password,
            PASSWORD_BCRYPT
        );
    }

    /**
     * @param string $password
     * @return bool
     */
    protected function isRawPassword(string $password): bool
    {
        $passwordInfo = password_get_info($password);

        return ($passwordInfo[static::KEY_PASSWORD_ALGO] === 0);
    }

    /**
     * @return string
     */
    protected function createMerchantUserKey(): string
    {
        return sprintf(
            '%s:%s',
            static::MERCHANT_USER_SESSION_KEY,
            static::KEY_CURRENT_MERCHANT_USER
        );
    }

    /**
     * @return \Generated\Shared\Transfer\MerchantUserTransfer|null
     */
    protected function readMerchantUserFromSession(): ?MerchantUserTransfer
    {
        $key = $this
            ->createMerchantUserKey();

        if ($this->session->has($key) === false) {
            return null;
        }

        return $this
            ->session
            ->get($key);
    }
}
