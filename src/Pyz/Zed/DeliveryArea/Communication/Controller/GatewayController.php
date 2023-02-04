<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 01.03.18
 * Time: 15:28
 */

namespace Pyz\Zed\DeliveryArea\Communication\Controller;


use ArrayObject;
use Exception;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaRequestTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\DeliveryArea\Communication\Controller
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getTimeSlotsForBranchesAction(AppApiRequestTransfer $requestTransfer) : AppApiResponseTransfer
    {
        $responseTransfer = new AppApiResponseTransfer();

        $requestTransfer->requireZipCode()->requireBranchIds();

        $timeSlots = $this
            ->getFacade()
            ->getConcreteTimeSlotsForBranchesAndZipCode(
                    $requestTransfer->getBranchIds(),
                    $requestTransfer->getZipCode(),
                    $requestTransfer->getMaxSlots(),
                    $requestTransfer->getItemsPerSlot()
                );

        $responseTransfer->setTimeSlots(new ArrayObject($timeSlots));

        return $responseTransfer;
    }

    /**
     * @param DeliveryAreaRequestTransfer $requestTransfer
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotByIdAction(DeliveryAreaRequestTransfer $requestTransfer) : ConcreteTimeSlotTransfer
    {
        return $this
            ->getFacade()
            ->getConcreteTimeSlotById($requestTransfer->getIdConcreteTimeSlot());

    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getDeliveryAreasByIdBranchAction(AppApiRequestTransfer $requestTransfer)
    {
        $deliveryAreas = $this
            ->getFacade()
            ->getDeliveryAreasByIdBranch($requestTransfer->getIdBranch());

        $responseTransfer = new AppApiResponseTransfer();
        $responseTransfer->setDeliveryAreas(new ArrayObject($deliveryAreas));

        return $responseTransfer;

    }

    /**
     * @param DeliveryAreaRequestTransfer $requestTransfer
     * @return DeliveryAreaTransfer
     */
    public function getCityNameByZipCodeAction(DeliveryAreaRequestTransfer $requestTransfer)
    {
        $responseTransfer = new DeliveryAreaTransfer();
        try {
            $deliveryArea = $this
                ->getFacade()
                ->getDeliveryAreaByZipCode($requestTransfer->getZipCode());
            $responseTransfer->setCity($deliveryArea->getCity());
            $responseTransfer->setZipValid(true);
        } catch (DeliveryAreaNotFoundException $e) {
            $responseTransfer->setCity('');
            $responseTransfer->setZipValid(false);
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\DeliveryAreaRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DeliveryAreaTransfer
     */
    public function getCityNameByZipOrBranchCodeAction(DeliveryAreaRequestTransfer $requestTransfer): DeliveryAreaTransfer
    {
        $responseTransfer = new DeliveryAreaTransfer();

        try {
            $deliveryArea = $this
                ->getFacade()
                ->getDeliveryAreaByZipOrBranchCode(
                    $requestTransfer
                        ->getZipCode(),
                    $requestTransfer
                        ->getBranchCode()
                );

            $responseTransfer
                ->setCity($deliveryArea->getCity());
            $responseTransfer
                ->setZipValid(true);
        } catch (\Exception $exception) {
            $responseTransfer->setCity('');
            $responseTransfer->setZipValid(false);
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     */
    public function getBranchDeliversZipCodeAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $response = new AppApiResponseTransfer();

        try {
            $hasDeliveryArea = $this
                ->getFacade()
                ->getDeliveryAreaByZipAndBranchCode(
                    $requestTransfer->getZipCode(),
                    $requestTransfer->getCode()
                );

            $response
                ->setZipValid(
                    $hasDeliveryArea
                );
        } catch (Exception $exception) {
            $response
                ->setZipValid(false);
        }

        return $response;
    }
}
