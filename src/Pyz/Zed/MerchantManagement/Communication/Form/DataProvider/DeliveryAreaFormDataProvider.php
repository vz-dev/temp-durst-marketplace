<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class DeliveryAreaFormDataProvider
{

    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * DeliveryAreaFormDataProvider constructor.
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     */
    public function __construct(DeliveryAreaFacadeInterface $deliveryAreaFacade)
    {
        $this->deliveryAreaFacade = $deliveryAreaFacade;
    }

    /**
     * @param int $idDeliveryArea
     *
     * @return array
     */
    public function getData($idDeliveryArea)
    {
        $deliveryAreaTransfer = $this
            ->deliveryAreaFacade
            ->getDeliveryAreaById($idDeliveryArea);

        $formData = $deliveryAreaTransfer->toArray();

        return $formData;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [];
    }
}
