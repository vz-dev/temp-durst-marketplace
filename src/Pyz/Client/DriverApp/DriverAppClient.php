<?php

namespace Pyz\Client\DriverApp;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Pyz\Client\DriverApp\DriverAppFactory getFactory()
 */
class DriverAppClient extends AbstractClient implements DriverAppClientInterface
{
    /**
     * @return \Pyz\Client\DriverApp\Zed\DriverAppStubInterface
     */
    protected function getZedStub()
    {
        return $this->getFactory()->createZedStub();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer
    {
        return $this
            ->getZedStub()
            ->getLatestRelease();
    }

    /**
     * {@inheritDoc}
     *
     * @param String $currentVersion
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool
    {
        return $this
            ->getZedStub()
            ->isUpdatable($currentVersion);
    }
}
