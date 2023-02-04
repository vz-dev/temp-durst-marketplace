<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:30
 */

namespace Pyz\Zed\Driver\Business\Model;

use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;
use Orm\Zed\Driver\Persistence\Map\DstDriverTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Driver\Business\Exception\DriverExistsException;
use Pyz\Zed\Driver\Business\Exception\DriverInvalidArgumentException;
use Pyz\Zed\Driver\Business\Exception\DriverNotExistsException;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydratorInterface;
use Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface;
use Pyz\Zed\Driver\Persistence\DriverQueryContainerInterface;

class Driver implements DriverInterface
{
    const DATE_FORMAT = 'd.m.Y';

    /**
     * @var \Pyz\Zed\Driver\Persistence\DriverQueryContainer
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface
     */
    protected $drivingLicenseHydrator;


    /**
     * @var \Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydratorInterface
     */
    protected $branchHydrator;


    /**
     * Driver constructor.
     *
     * @param \Pyz\Zed\Driver\Persistence\DriverQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface $drivingLicenseHydrator
     */
    public function __construct(
        DriverQueryContainerInterface $queryContainer,
        DrivingLicenseHydratorInterface $drivingLicenseHydrator,
        BranchHydratorInterface $branchHydrator
    ) {
        $this->queryContainer = $queryContainer;
        $this->drivingLicenseHydrator = $drivingLicenseHydrator;
        $this->branchHydrator = $branchHydrator;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriver
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Pyz\Zed\Driver\Business\Exception\DriverNotExistsException
     */
    public function getDriverById(int $idDriver): DriverTransfer
    {
        $driverEntity = $this
            ->queryContainer
            ->queryDriver()
            ->findOneByIdDriver($idDriver);

        if ($driverEntity === null) {
            throw new DriverNotExistsException(
                sprintf(DriverNotExistsException::MESSAGE, $idDriver)
            );
        }

        return $this->entityToTransfer($driverEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $branch
     * @return \Generated\Shared\Transfer\DriverTransfer[]
     */
    public function getDriversByFkBranch(int $branch): array
    {
        $driverEntities = $this
            ->queryContainer
            ->queryDriver()
            ->filterByFkBranch($branch)
            ->filterByStatus(DstDriverTableMap::COL_STATUS_ACTIVE);

        $driverTransfers = [];
        foreach ($driverEntities as $driverEntity) {
            $driverTransfers[] = $this->entityToTransfer($driverEntity);
        }

        return $driverTransfers;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Pyz\Zed\Driver\Business\Exception\DriverInvalidArgumentException
     */
    public function save(DriverTransfer $driverTransfer): DriverTransfer
    {
        if ($driverTransfer->getFkBranch() === null) {
            throw new DriverInvalidArgumentException(DriverInvalidArgumentException::NO_FK_BRANCH_MESSAGE);
        }

        $driverEntity = $this->findEntityOrCreate($driverTransfer);

        $driverTransfer = $this->setPassword($driverTransfer);
        $driverEntity->fromArray($driverTransfer->toArray());
        $this->checkUnique($driverEntity);

        if ($driverEntity->isNew() || $driverEntity->isModified()) {
            $driverEntity->save();
        }

        return $this->entityToTransfer($driverEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function setPassword(DriverTransfer $driverTransfer): DriverTransfer
    {
        $password = $driverTransfer
            ->getPassword();

        $passwordInfo = password_get_info($password);

        if ($passwordInfo['algo'] !== PASSWORD_BCRYPT) {
            $driverTransfer->setPassword(
                password_hash(
                    $password,
                    PASSWORD_BCRYPT
                )
            );
        }

        return $driverTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriver
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function removeDriver(int $idDriver): DriverTransfer
    {
        $driverEntity = $this->getDriverById($idDriver);
        $driverEntity->setStatus(DstDriverTableMap::COL_STATUS_DELETED);

        return $this->save($driverEntity);
    }

    /**
     * @param DstDriver $driverEntity
     *
     * @return DriverTransfer
     * @throws PropelException
     */
    public function entityToTransfer(DstDriver $driverEntity): DriverTransfer
    {
        $driverTransfer = new DriverTransfer();
        $driverTransfer->fromArray($driverEntity->toArray(), true);

        $this
            ->drivingLicenseHydrator
            ->hydrateDriverByDrivingLicence($driverEntity, $driverTransfer);

        $this
            ->branchHydrator
            ->hydrateDriverByBranch($driverEntity, $driverTransfer);

        return $driverTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return \Orm\Zed\Driver\Persistence\DstDriver
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findEntityOrCreate(DriverTransfer $driverTransfer): DstDriver
    {
        if ($driverTransfer->getIdDriver() === null) {
            return new DstDriver();
        }

        return $this
            ->queryContainer
            ->queryDriverById($driverTransfer->getIdDriver())
            ->findOneOrCreate();
    }

    /**
     * @param \Orm\Zed\Driver\Persistence\DstDriver $entity
     *
     * @throws \Pyz\Zed\Driver\Business\Exception\DriverExistsException
     *
     * @return void
     */
    protected function checkUnique(DstDriver $entity)
    {
        if ($entity->isNew() && ($entity->getIdDriver() !== null)) {
            throw new DriverExistsException(
                sprintf(
                    DriverExistsException::MESSAGE,
                    $entity->getIdDriver()
                )
            );
        }
    }

    /**
     * @param string $email
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Pyz\Zed\Driver\Business\Exception\DriverNotExistsException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDriverByEmail(string $email): DriverTransfer
    {
        $driverEntity = $this
            ->queryContainer
            ->queryDriver()
            ->filterByStatus(DstDriverTableMap::COL_STATUS_ACTIVE)
            ->findOneByEmail($email);

        if ($driverEntity === null) {
            throw new DriverNotExistsException(sprintf(
                DriverNotExistsException::EMAIL_MESSAGE,
                $email
            ));
        }

        return $this
            ->entityToTransfer($driverEntity);
    }

    /**
     * @param int $idBranch
     * @param int[] $idDriversToExclude
     * @return DriverTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDriversFromBranchWithExcludedDrivers(int $idBranch, array $idDriversToExclude): array
    {
        $drivers = [];

        $driverEntities = $this
            ->queryContainer
            ->queryActiveDriversByFkBranchAndExcludedDrivers(
                $idBranch,
                $idDriversToExclude
            )
            ->find();

        foreach ($driverEntities as $driverEntity) {
            $drivers[] = $this
                ->entityToTransfer($driverEntity);
        }

        return $drivers;
    }
}
