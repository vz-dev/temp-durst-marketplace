<?php
/**
 * Durst - project - BranchUser.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:47
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchUserTransfer;
use Orm\Zed\Merchant\Persistence\DstBranchUser;
use Orm\Zed\Merchant\Persistence\Map\DstBranchUserTableMap;
use Pyz\Zed\Merchant\Business\Exception\BranchUserEmailNotUniqueException;
use Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException;
use Pyz\Zed\Merchant\MerchantConfig;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BranchUser implements BranchUserInterface
{
    public const BRANCH_USER_SESSION_KEY = 'branchuser';
    public const KEY_CURRENT_BRANCH_USER = 'currentBranchUser';

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
     * @var array|\Pyz\Zed\Merchant\Communication\Plugin\BranchUserHydratorPluginInterface[]
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
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     */
    public function getCurrentBranchUser(): BranchUserTransfer
    {
        $branchUser = $this
            ->readBranchUserFromSession();

        if ($branchUser === null) {
            throw new BranchUserNotFoundException(
                BranchUserNotFoundException::MESSAGE_NOT_IN_SESSION
            );
        }

        return clone $branchUser;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return mixed
     */
    public function setCurrentBranchUser(BranchUserTransfer $branchUserTransfer)
    {
        $key = $this
            ->createBranchUserKey();

        return $this
            ->session
            ->set(
                $key,
                clone $branchUserTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentBranchUser(): void
    {
        $key = $this
            ->createBranchUserKey();

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
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserEmailNotUniqueException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(BranchUserTransfer $branchUserTransfer): BranchUserTransfer
    {
        $branchUserEntity = $this
            ->queryContainer
            ->queryBranchUserById($branchUserTransfer->getIdBranchUser())
            ->findOneOrCreate();

        $this
            ->hydrateEntity(
                $branchUserEntity,
                $branchUserTransfer
            );

        $this
            ->checkUnique($branchUserEntity);

        if (
            $branchUserEntity->isNew() === true ||
            $branchUserEntity->isModified() === true
        ) {
            $branchUserEntity
                ->save();
        }

        return $this
            ->entityToTransfer(
                $branchUserEntity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     */
    public function deleteBranchUser(int $idBranchUser): bool
    {
        $branchUser = $this
            ->queryContainer
            ->queryBranchUserById($idBranchUser)
            ->findOne();

        if ($branchUser === null || $branchUser->getIdBranchUser() === null) {
            throw new BranchUserNotFoundException(
                sprintf(
                    BranchUserNotFoundException::MESSAGE,
                    $idBranchUser
                )
            );
        }

        $branchUser
            ->setStatus(DstBranchUserTableMap::COL_STATUS_DELETED);
        $rowsAffected = $branchUser
            ->save();

        return ($rowsAffected > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     */
    public function activateBranchUser(int $idBranchUser): bool
    {
        $branchUser = $this
            ->queryContainer
            ->queryBranchUserById($idBranchUser)
            ->findOne();

        if ($branchUser === null || $branchUser->getIdBranchUser() === null) {
            throw new BranchUserNotFoundException(
                sprintf(
                    BranchUserNotFoundException::MESSAGE,
                    $idBranchUser
                )
            );
        }

        $branchUser
            ->setStatus(DstBranchUserTableMap::COL_STATUS_ACTIVE);

        $rowsAffected = $branchUser
            ->save();

        return ($rowsAffected > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     */
    public function deactivateBranchUser(int $idBranchUser): bool
    {
        $branchUser = $this
            ->queryContainer
            ->queryBranchUserById($idBranchUser)
            ->findOne();

        if ($branchUser === null || $branchUser->getIdBranchUser() === null) {
            throw new BranchUserNotFoundException(
                sprintf(
                    BranchUserNotFoundException::MESSAGE,
                    $idBranchUser
                )
            );
        }

        $branchUser
            ->setStatus(DstBranchUserTableMap::COL_STATUS_BLOCKED);

        $rowsAffected = $branchUser
            ->save();

        return ($rowsAffected > 0);
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
        return password_verify($password, $hash);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function logout(): void
    {
        $key = $this
            ->createBranchUserKey();

        if ($this->session->has($key) !== false) {
            $this
                ->session
                ->remove($key);
            $this
                ->session
                ->migrate();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchUserById(int $idBranchUser): BranchUserTransfer
    {
        $branchUser = $this
            ->queryContainer
            ->queryBranchUserById($idBranchUser)
            ->filterByStatus(DstBranchUserTableMap::COL_STATUS_ACTIVE)
            ->findOne();

        if ($branchUser->getIdBranchUser() === null) {
            throw new BranchUserNotFoundException(
                sprintf(
                    BranchUserNotFoundException::MESSAGE,
                    $idBranchUser
                )
            );
        }

        return $this
            ->entityToTransfer($branchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchUserByEmail(string $email): BranchUserTransfer
    {
        $branchUser = $this
            ->queryContainer
            ->queryBranchUserByEmail($email)
            ->filterByStatus(DstBranchUserTableMap::COL_STATUS_ACTIVE)
            ->findOne();

        if ($branchUser->getIdBranchUser() === null) {
            throw new BranchUserNotFoundException(
                sprintf(
                    BranchUserNotFoundException::MESSAGE_EMAIL,
                    $email
                )
            );
        }

        return $this
            ->entityToTransfer($branchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasActiveBranchUserByEmail(string $email): bool
    {
        $amount = $this
            ->queryContainer
            ->queryBranchUserByEmail($email)
            ->filterByStatus(DstBranchUserTableMap::COL_STATUS_ACTIVE)
            ->count();

        return ($amount > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function hasCurrentBranchUser(): bool
    {
        $branchUser = $this
            ->readBranchUserFromSession();

        return ($branchUser !== null);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return BranchUserTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchUsersByIdBranch(int $idBranch): array
    {
        $branchUserEntities = $this
            ->queryContainer
            ->queryBranchUser()
            ->filterByFkBranch($idBranch)
            ->find();

        $branchUsers = [];

        foreach ($branchUserEntities as $branchUserEntity) {
            $branchUsers[] = $this
                ->entityToTransfer($branchUserEntity);
        }

        return $branchUsers;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    protected function entityToTransfer(DstBranchUser $branchUser): BranchUserTransfer
    {
        $branchUserTransfer = new BranchUserTransfer();
        $branchUserTransfer
            ->fromArray(
                $branchUser->toArray(),
                true
            );

        $this
            ->hydrateBranchUser(
                $branchUser,
                $branchUserTransfer
            );

        return $branchUserTransfer;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     */
    protected function hydrateBranchUser(
        DstBranchUser $branchUser,
        BranchUserTransfer $branchUserTransfer
    ): void
    {
        foreach ($this->hydratorPlugins as $hydratorPlugin) {
            $hydratorPlugin
                ->hydrateBranchUser(
                    $branchUser,
                    $branchUserTransfer
                );
        }
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserEmailNotUniqueException
     */
    protected function checkUnique(DstBranchUser $branchUser): void
    {
        if ($branchUser->isNew() === true) {
            if (
                $this->hasMerchantUserByEmail($branchUser->getEmail()) === true ||
                $this->hasBranchUserByEmail($branchUser->getEmail()) === true ||
                $this->hasMerchantByEmail($branchUser->getEmail()) === true
            ) {
                throw new BranchUserEmailNotUniqueException(
                    sprintf(
                        BranchUserEmailNotUniqueException::MESSAGE,
                        $branchUser->getEmail()
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
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hydrateEntity(
        DstBranchUser $branchUser,
        BranchUserTransfer $branchUserTransfer
    ): void
    {
        $branchUser->setSalutation($branchUserTransfer->getSalutation());
        $branchUser->setFirstName($branchUserTransfer->getFirstName());
        $branchUser->setLastName($branchUserTransfer->getLastName());
        $branchUser->setEmail($branchUserTransfer->getEmail());
        $branchUser->setFkBranch($branchUserTransfer->getFkBranch());
        $branchUser->setFkAclGroup($branchUserTransfer->getFkAclGroup());

        $password = $branchUserTransfer->getPassword();
        if (
            !empty($password) &&
            $this->isRawPassword($password)
        ) {
            $branchUser->setPassword(
                $this->encryptPassword($password)
            );
        }

        if ($branchUserTransfer->getStatus() !== null) {
            $branchUser->setStatus($branchUserTransfer->getStatus());
        } else {
            $branchUser->setStatus(DstBranchUserTableMap::COL_STATUS_BLOCKED);
        }

        if ($branchUserTransfer->getLastLogin() !== null) {
            $branchUser->setLastLogin($branchUserTransfer->getLastLogin());
        }
    }

    /**
     * @param string $password
     * @return string
     */
    protected function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
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
    protected function createBranchUserKey(): string
    {
        return sprintf(
            '%s:%s',
            static::BRANCH_USER_SESSION_KEY,
            static::KEY_CURRENT_BRANCH_USER
        );
    }

    /**
     * @return \Generated\Shared\Transfer\BranchUserTransfer|null
     */
    protected function readBranchUserFromSession(): ?BranchUserTransfer
    {
        $key = $this
            ->createBranchUserKey();

        if ($this->session->has($key) === false) {
            return null;
        }

        return $this
            ->session
            ->get($key);
    }
}
