<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:12
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Controller;

use Generated\Shared\Transfer\AppApiResponseTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\HeidelpayRest\Communication\Controller
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $requestTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getStatusForAuthorizationAction(TransferInterface $requestTransfer)
    {
        /** @var \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer $requestTransfer */
        return $this
            ->getFacade()
            ->getAuthorizationStatusByPaymentId($requestTransfer->getPaymentId());
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $requestTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getStatusForAuthorizationByOrderRefAction(TransferInterface $requestTransfer)
    {
        /** @var \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer $requestTransfer */
        return $this
            ->getFacade()
            ->getAuthorizationStatusBySalesOrderRef($requestTransfer->getOrderRef());
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     */
    public function getSepaMandateUrlAction(TransferInterface $requestTransfer)
    {
        $responseTransfer = new AppApiResponseTransfer();

        $responseTransfer->setSepaMandateUrl($this
            ->getFactory()
            ->getConfig()
            ->getSepaMandateUrl());

        return $responseTransfer;
    }
}
