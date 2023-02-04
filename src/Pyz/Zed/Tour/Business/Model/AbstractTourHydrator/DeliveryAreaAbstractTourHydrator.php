<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 08.10.18
 * Time: 14:04
 */

namespace Pyz\Zed\Tour\Business\Model\AbstractTourHydrator;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;

class DeliveryAreaAbstractTourHydrator implements AbstractTourHydratorInterface
{
    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * DeliveryAreaHydrator constructor.
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     */
    public function __construct(DeliveryAreaFacadeInterface $deliveryAreaFacade)
    {
        $this->deliveryAreaFacade = $deliveryAreaFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     */
    public function hydrateAbstractTour(
        DstAbstractTour $abstractTourEntity,
        AbstractTourTransfer $abstractTourTransfer)
    {
        $deliveryAreaIds = [];
        $deliveryAreaTransfers = [];

        $cmpDeliveryAreasByZip = function (DeliveryAreaTransfer $first, DeliveryAreaTransfer $second) {
            return strcmp($first->getZip(), $second->getZip());
        };

        foreach ($abstractTourEntity->getDstAbstractTourToAbstractTimeSlots() as $abstractTourToAbstractTimeSlot) {
            $idDeliveryArea = $abstractTourToAbstractTimeSlot
                ->getSpyTimeSlot()
                ->getFkDeliveryArea();

            if (!in_array($idDeliveryArea, $deliveryAreaIds)){
                $deliveryAreaTransfer = $this
                    ->deliveryAreaFacade
                    ->convertDeliveryAreaEntityToTransfer(
                        $abstractTourToAbstractTimeSlot->getSpyTimeSlot()->getSpyDeliveryArea()
                    );

                $deliveryAreaIds[] = $idDeliveryArea;
                $deliveryAreaTransfers[] = $deliveryAreaTransfer;
            }
        }

        usort($deliveryAreaTransfers, $cmpDeliveryAreasByZip);
        foreach ($deliveryAreaTransfers as $deliveryAreaTransfer){
            $abstractTourTransfer->addDeliveryAreas($deliveryAreaTransfer);
        }
    }

}
