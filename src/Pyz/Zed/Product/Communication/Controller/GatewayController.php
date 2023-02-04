<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 09:52
 */

namespace Pyz\Zed\Product\Communication\Controller;


use ArrayObject;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\Product\Communication\ProductCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;


/**
 * Class GatewayController
 * @package Pyz\Zed\Product\Communication\Controller
 * @method ProductCommunicationFactory getFactory()
 * @method ProductFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAllProductGtinsForDriverAppAction(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        $driverGtinResponse = new DriverAppApiResponseTransfer();

        $driverGtinTransfers = $this
            ->getFacade()
            ->getGtins();

        $driverGtins = new ArrayObject($driverGtinTransfers);

        $driverGtinResponse
            ->setGtins($driverGtins);

        return $driverGtinResponse;
    }
}
