<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 19.06.18
 * Time: 09:29
 */

namespace Pyz\Zed\DeliveryArea\Business\Hydrator;


use Generated\Shared\Transfer\OrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlot;

/**
 * Class ConcreteTimeSlotOrderHydrator
 * @package Pyz\Zed\DeliveryArea\Business\Hydrator
 */
class ConcreteTimeSlotOrderHydrator
{
    /**
     * @var ConcreteTimeSlot
     */
    protected $concreteTimeSlotModel;

    /**
     * ConcreteTimeSlotOrderHydrator constructor.
     * @param ConcreteTimeSlot $concreteTimeSlotModel
     */
    public function __construct(ConcreteTimeSlot $concreteTimeSlotModel)
    {
        $this->concreteTimeSlotModel = $concreteTimeSlotModel;
    }


    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws PropelException
     * @throws ConcreteTimeSlotNotFoundException
     */
    public function hydrateOrderTransferWithConcreteTimeSlotTransfer(OrderTransfer $orderTransfer)
    {
        $concreteId = $orderTransfer->getFkConcreteTimeslot();

        if($concreteId !== null)
        {
            $concreteTransfer = $this
                ->concreteTimeSlotModel
                ->getConcreteTimeSlotById($concreteId);

            $orderTransfer->setConcreteTimeSlot($concreteTransfer);
        }

        return $orderTransfer;
    }

}
