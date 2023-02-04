<?php
/**
 * Durst - project - PassedConcreteTimeSlotDeleteToucher.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.11.18
 * Time: 10:24
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;

use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class PassedConcreteTimeSlotDeleteToucher implements PassedConcreteTimeSlotDeleteToucherInterface
{
    /**
     * @var \Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface
     */
    protected $touchFacade;

    /**
     * @var \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface
     */
    protected $deliveryAreaQueryContainer;

    /**
     * PassedConcreteTimeSlotDeleteToucher constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface $touchFacade
     * @param \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
     */
    public function __construct(
        DeliveryAreaToTouchBridgeInterface $touchFacade,
        DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
    ) {
        $this->touchFacade = $touchFacade;
        $this->deliveryAreaQueryContainer = $deliveryAreaQueryContainer;
    }

    /**
     * @return void
     */
    public function touchPassedConcreteTimeSlotsToDelete()
    {
        foreach ($this->getPassedConcreteTimeSlots() as $passedConcreteTimeSlotEntity) {
            $this
                ->touchByIdConcreteTimeSlot(
                    $passedConcreteTimeSlotEntity->getIdConcreteTimeSlot()
                );
        }
    }

    /**
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot[]
     */
    protected function getPassedConcreteTimeSlots(): iterable
    {
        $passedConcreteTimeSlotEntities = $this
            ->deliveryAreaQueryContainer
            ->queryConcreteTimeSlot()
            ->filterByStartTime(Criteria::CURRENT_TIMESTAMP, Criteria::LESS_THAN)
            ->find();

        return $passedConcreteTimeSlotEntities;
    }

    /**
     * @param int $idConcreteTimeSlot
     *
     * @return bool
     */
    protected function touchByIdConcreteTimeSlot(int $idConcreteTimeSlot): bool
    {
        return $this
            ->touchFacade
            ->touchDeleted(
                DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT,
                $idConcreteTimeSlot
            );
    }
}
