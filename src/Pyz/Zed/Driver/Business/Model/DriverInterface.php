<?php
/**
 * Durst - project - DriverInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-20
 * Time: 10:48
 */

namespace Pyz\Zed\Driver\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;
use Pyz\Zed\Driver\Business\Exception\DriverExistsException;
use Pyz\Zed\Driver\Business\Exception\DriverInvalidArgumentException;
use Pyz\Zed\Driver\Business\Exception\DriverNotExistsException;

interface DriverInterface
{
    /**
     * @param int $idDriver
     * @return DriverTransfer
     * @throws DriverNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDriverById(int $idDriver) : DriverTransfer;

    /**
     * @param int $branch
     * @return DriverTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDriversByFkBranch(int $branch) : array;

    /**
     * @param DriverTransfer $driverTransfer
     * @return DriverTransfer
     * @throws DriverExistsException
     * @throws DriverInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function save(DriverTransfer $driverTransfer) : DriverTransfer;

    /**
     * @param int $idDriver
     * @return DriverTransfer
     * @throws DriverExistsException
     * @throws DriverInvalidArgumentException
     * @throws DriverNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeDriver(int $idDriver) : DriverTransfer;

    /**
     * @param string $email
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByEmail(string $email): DriverTransfer;

    /**
     * @param int $idBranch
     * @param int[] $idDriversToExclude
     * @return DriverTransfer[]
     */
    public function getDriversFromBranchWithExcludedDrivers(int $idBranch, array $idDriversToExclude): array;
}
