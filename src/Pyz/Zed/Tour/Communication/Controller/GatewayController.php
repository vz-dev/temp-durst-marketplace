<?php


namespace Pyz\Zed\Tour\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class GatewayController
 * @package Pyz\Zed\Tour\Communication\Controller
 * @method TourCommunicationFactory getFactory()
 * @method TourFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     *
     * @return DriverAppApiResponseTransfer
     */
    public function getToursWithOrdersAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverResponse = new DriverAppApiResponseTransfer();

        $token = $requestTransfer
            ->getToken();

        $driver = $this
            ->getFactory()
            ->getAuthFacade()
            ->getDriverByToken($token);

        $tourOrders = $this
            ->getFacade()
            ->getOrdersWithToursByDriver($driver);

        $orders = new ArrayObject($tourOrders);

        $driverResponse
            ->setOrders($orders);

        return $driverResponse;
    }

    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     *
     * @return DriverAppApiResponseTransfer
     *
     * @throws ContainerKeyNotFoundException
     */
    public function getToursForDriverAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $token = $requestTransfer
            ->getToken();

        $driver = $this
            ->getFactory()
            ->getAuthFacade()
            ->getDriverByToken($token);

        $branchUsesGraphmasters = $this
            ->getFactory()
            ->getGraphMastersFacade()
            ->doesBranchUseGraphmasters($driver->getFkBranch());

        // @TODO: Support mixed tours for transitional phase
        $tours = $this
            ->getFacade()
            ->getToursForDriver($driver);

        if($branchUsesGraphmasters === true){
            $gmTours = $this
                ->getFactory()
                ->getGraphMastersFacade()
                ->getToursForDriver($driver);

            $tours = array_merge($tours, $gmTours);
        }

        return (new DriverAppApiResponseTransfer())
            ->setTours(new ArrayObject($tours));
    }
}
