<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-06
 * Time: 10:36
 */

namespace Pyz\Zed\Deposit\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Deposit\Communication\DepositCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class GatewayController
 * @package Pyz\Zed\Deposit\Communication\Controller
 * @method DepositCommunicationFactory getFactory()
 * @method DepositFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param AppApiRequestTransfer $requestTransfer
     *
     * @return AppApiResponseTransfer
     */
    public function getWeightForApiRequestAction(AppApiRequestTransfer $requestTransfer)
    {
        $weight = $this
            ->getFacade()
            ->getWeightForApiRequestItems($requestTransfer);

        return (new AppApiResponseTransfer())
            ->setRequestWeight($weight);
    }

    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getAllDepositsForDriverAppAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverDepositResponse = new DriverAppApiResponseTransfer();

        $driverAuth = $this
            ->getFactory()
            ->getMerchantFacade()
            ->hasActiveMerchantByMerchantPin($requestTransfer->getMerchantPin());

        $driverDepositResponse
            ->setAuthValid($driverAuth);

        if ($driverAuth === true) {
            $driverDepositTransfers = $this
                ->getFacade()
                ->getDeposits();

            $driverDeposits = new ArrayObject($driverDepositTransfers);

            $driverDepositResponse
                ->setDeposits($driverDeposits);
        }

        return $driverDepositResponse;
    }
}
