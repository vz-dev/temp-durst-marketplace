<?php

namespace Pyz\Zed\DriverApp\Communication\Controller;

use Generated\Shared\Transfer\VersionCheckTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Pyz\Zed\DriverApp\Business\DriverAppFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestReleaseAction(TransferInterface $transfer)
    {
        return $this
            ->getFacade()
            ->getLatestRelease();
    }

    /**
     * @param \Generated\Shared\Transfer\VersionCheckTransfer $transfer
     *
     * @return \Generated\Shared\Transfer\VersionCheckTransfer
     */
    public function isUpdatableAction(VersionCheckTransfer $transfer)
    {
        $transfer
            ->setUpdatable($this->getFacade()->isUpdatable($transfer->getCurrentVersion()));

        return $transfer;
    }
}
