<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 11:15
 */

namespace Pyz\Zed\Driver\Business;


use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;

interface DriverFacadeInterface
{
    /**
     * Returns a fully hydrated transfer object matching the driver in the data base with the given id.
     *
     * @param int $idDriver
     * @return DriverTransfer
     */
    public function getDriverById(int $idDriver) : DriverTransfer;

    /**
     * Adds the given driver transfer to the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param DriverTransfer $driverTransfer
     * @return DriverTransfer
     */
    public function addDriver(DriverTransfer $driverTransfer) : DriverTransfer;

    /**
     * Updates the given driver transfer in the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param DriverTransfer $driverTransfer
     * @return DriverTransfer
     */
    public function updateDriver(DriverTransfer $driverTransfer) : DriverTransfer;

    /**
     * Returns an array of transfer objects representing all drivers in the database
     * with status active and referring to the given fkBranch.
     *
     * @param int $branch
     * @return DriverTransfer[]
     */
    public function getDriversByFkBranch(int $branch) : array;

    /**
     * Sets the status of a driver given by its id in the database to deleted.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param int $idDriver
     * @return DriverTransfer
     */
    public function removeDriver(int $idDriver) : DriverTransfer;

    /**
     * Returns a fully hydrated transfer object matching the driver in the database with the given email.
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByEmail(string $email): DriverTransfer;

    /**
     * Return a list of drivers for a given branch
     * but NOT if the id is inside the given array
     *
     * @param int $idBranch
     * @param int[] $idDriversToExclude
     * @return DriverTransfer[]
     */
    public function getDriversFromBranchWithExcludedDrivers(int $idBranch, array $idDriversToExclude): array;

    /**
     * Converts a driver entity to a transfer object
     *
     * @param DstDriver $driverEntity
     * @return DriverTransfer
     */
    public function convertDriverEntityToTransfer(DstDriver $driverEntity): DriverTransfer;
}
