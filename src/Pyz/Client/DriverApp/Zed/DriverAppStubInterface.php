<?php

namespace Pyz\Client\DriverApp\Zed;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;

interface DriverAppStubInterface
{
    /**
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer;

    /**
     * @param String $currentVersion
     *
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool;
}
