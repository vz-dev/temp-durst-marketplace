<?php

namespace Pyz\Client\DriverApp\Zed;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Generated\Shared\Transfer\VersionCheckTransfer;
use Spryker\Client\ZedRequest\Stub\ZedRequestStub;

class DriverAppStub extends ZedRequestStub implements DriverAppStubInterface
{
    protected const URL_GET_LATEST_RELEASE = '/driver-app/gateway/get-latest-release';
    protected const URL_IS_UPDATABLE = '/driver-app/gateway/is-updatable';

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer
    {
        /** @var \Generated\Shared\Transfer\DriverAppReleaseTransfer $releaseTransfer */
        $releaseTransfer = $this
            ->zedStub
            ->call(
                self::URL_GET_LATEST_RELEASE,
                new DriverAppReleaseTransfer()
            );

        return $releaseTransfer;
    }

    /**
     * @param String $currentVersion
     *
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool
    {
        /** @var \Generated\Shared\Transfer\VersionCheckTransfer $versionCheckTransfer */
        $versionCheckTransfer = $this
            ->zedStub
            ->call(
                static::URL_IS_UPDATABLE,
                $this->createVersionCheckTransfer($currentVersion)
            );

        return $versionCheckTransfer->getUpdatable();
    }

    /**
     * @param string $currentVersion
     *
     * @return \Generated\Shared\Transfer\VersionCheckTransfer
     */
    protected function createVersionCheckTransfer(string $currentVersion): VersionCheckTransfer
    {
        return (new VersionCheckTransfer())
            ->setCurrentVersion($currentVersion);
    }
}
