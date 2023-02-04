<?php
/**
 * Durst - project - DepositMerchantConnectorGatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:45
 */

namespace Pyz\Zed\DepositMerchantConnector\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Pyz\Zed\DepositMerchantConnector\Business\DepositMerchantConnectorFacadeInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class DepositMerchantConnectorGatewayController
 * @package Pyz\Zed\DepositMerchantConnector\Communication\Controller
 * @method DepositMerchantConnectorFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $transfer
     *
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function getDepositsForBranchAction(DriverAppApiRequestTransfer $transfer)
    {
        $deposits = $this
            ->getFacade()
            ->getDepositsForBranch(
                $transfer->getBranch()
            );

        return (new DriverAppApiResponseTransfer())
            ->setDeposits(
                new ArrayObject($deposits)
            );
    }
}
