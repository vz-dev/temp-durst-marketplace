<?php

namespace Pyz\Zed\DriverApp\Business;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;

interface DriverAppFacadeInterface
{
    /**
     * Specification:
     *  - Adds a release to the database
     *  - Version must be unique
     *  - Returns an updated version of the release transfer with id set
     *
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $driverAppReleaseTransfer
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function addRelease(DriverAppReleaseTransfer $driverAppReleaseTransfer): DriverAppReleaseTransfer;

    /**
     * Specification:
     *  - Removes the release with the matching id from the database
     *  - if no release with the given id exists an exception is beeing thrown
     *
     * @param int $idDriverAppRelease
     *
     * @return void
     */
    public function deleteRelease(int $idDriverAppRelease): void;

    /**
     * Specification:
     *  - loads the release with the given id from the database
     *  - returns a mapped transfer of the release
     *  - if no release with the given id exists an exception is beeing thrown
     *
     * @param int $idDriverAppRelease
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getReleaseById(int $idDriverAppRelease): DriverAppReleaseTransfer;

    /**
     * Specification:
     *  - Returns the release that was created last
     *      (! not the one with the highest version number !)
     *  - If no release is present an empty transfer will be returned
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer;

    /**
     * Specification:
     *  - Checks whether the given version string equals the one of the last added APK
     *  - Returns true if the current version doesn't match the one of the latest APK
     *
     * @param String $currentVersion
     *
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool;
}
