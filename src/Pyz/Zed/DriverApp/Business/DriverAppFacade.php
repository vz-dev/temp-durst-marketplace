<?php

namespace Pyz\Zed\DriverApp\Business;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\DriverApp\Business\DriverAppBusinessFactory getFactory()
 */
class DriverAppFacade extends AbstractFacade implements DriverAppFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $driverAppReleaseTransfer
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function addRelease(DriverAppReleaseTransfer $driverAppReleaseTransfer): DriverAppReleaseTransfer
    {
        return $this
            ->getFactory()
            ->createReleaseModel()
            ->addRelease($driverAppReleaseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriverAppRelease
     */
    public function deleteRelease(int $idDriverAppRelease): void
    {
        $this
            ->getFactory()
            ->createReleaseModel()
            ->deleteRelease($idDriverAppRelease);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriverAppRelease
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getReleaseById(int $idDriverAppRelease): DriverAppReleaseTransfer
    {
        return $this
            ->getFactory()
            ->createReleaseModel()
            ->getReleaseById($idDriverAppRelease);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer
    {
        return $this
            ->getFactory()
            ->createReleaseModel()
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
            ->getFactory()
            ->createReleaseModel()
            ->isUpdatable($currentVersion);
    }
}
