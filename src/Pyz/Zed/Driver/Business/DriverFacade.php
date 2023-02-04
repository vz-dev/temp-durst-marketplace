<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 11:14
 */

namespace Pyz\Zed\Driver\Business;


use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method DriverBusinessFactory getFactory()
 */
class DriverFacade extends AbstractFacade implements DriverFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idDriver
     * @return DriverTransfer
     * @throws Exception\DriverNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDriverById(int $idDriver) : DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->getDriverById($idDriver);
    }

    /**
     * {@inheritdoc}
     *
     * @param DriverTransfer $driverTransfer
     * @return DriverTransfer
     * @throws Exception\DriverExistsException
     * @throws Exception\DriverInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addDriver(DriverTransfer $driverTransfer) : DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->save($driverTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param DriverTransfer $driverTransfer
     * @return DriverTransfer
     * @throws Exception\DriverExistsException
     * @throws Exception\DriverInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updateDriver(DriverTransfer $driverTransfer) : DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->save($driverTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $branch
     * @return DriverTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDriversByFkBranch(int $branch) : array
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->getDriversByFkBranch($branch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDriver
     * @return DriverTransfer
     * @throws Exception\DriverExistsException
     * @throws Exception\DriverInvalidArgumentException
     * @throws Exception\DriverNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeDriver(int $idDriver) : DriverTransfer
    {
        return $this->getFactory()
            ->createDriverModel()
            ->removeDriver($idDriver);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Pyz\Zed\Driver\Business\Exception\DriverNotExistsException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDriverByEmail(string $email): DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->getDriverByEmail($email);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param int[] $idDriversToExclude
     * @return DriverTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDriversFromBranchWithExcludedDrivers(int $idBranch, array $idDriversToExclude): array
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->getDriversFromBranchWithExcludedDrivers(
                $idBranch,
                $idDriversToExclude
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param DstDriver $driverEntity
     * @return DriverTransfer
     */
    public function convertDriverEntityToTransfer(DstDriver $driverEntity): DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverModel()
            ->entityToTransfer($driverEntity);
    }
}
