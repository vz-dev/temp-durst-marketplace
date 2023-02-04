<?php

namespace Pyz\Zed\DeliveryArea\Business\Writer;

use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class DeliveryAreaWriter
{
    //TODO why are there two classes as branch model? Aren't they redundant?

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;


    /**
     * DeliveryAreaWriter constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     */
    public function __construct(DeliveryAreaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return bool
     */
    public function deliveryAreasAreImported()
    {
        return $this->queryContainer->queryDeliveryArea()->count() > 0;
    }

    /**
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createDeliveryArea(DeliveryAreaTransfer $deliveryAreaTransfer)
    {
        $deliveryAreaEntity = new SpyDeliveryArea();
        $deliveryAreaEntity->fromArray($deliveryAreaTransfer->toArray());
        $deliveryAreaEntity->setZipCode($deliveryAreaTransfer->getZip());
        $deliveryAreaEntity->save();
    }

}