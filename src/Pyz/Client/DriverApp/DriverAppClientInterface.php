<?php

namespace Pyz\Client\DriverApp;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;

interface DriverAppClientInterface
{
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
