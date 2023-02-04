<?php
/**
 * Durst - project - ConcreteTimeSlotDeleteToucher.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.10.20
 * Time: 08:46
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class ConcreteTimeSlotDeleteToucher implements ConcreteTimeSlotDeleteToucherInterface
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
     * ConcreteTimeSlotDeleteToucher constructor.
     * @param \Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface $touchFacade
     * @param \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
     */
    public function __construct(
        DeliveryAreaToTouchBridgeInterface $touchFacade,
        DeliveryAreaQueryContainerInterface $deliveryAreaQueryContainer
    )
    {
        $this->touchFacade = $touchFacade;
        $this->deliveryAreaQueryContainer = $deliveryAreaQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTimeSlot
     * @param int $idBranch
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function deleteConcreteTimeSlotByIdAndBranch(
        int $idConcreteTimeSlot,
        int $idBranch
    ): void
    {
        $concreteTimeslot = $this
            ->getConcreteTimeslot(
                $idConcreteTimeSlot,
                $idBranch
            );

        $this
            ->touchByIdConcreteTimeSlot(
                $concreteTimeslot
                    ->getIdConcreteTimeSlot()
            );

        $concreteTimeslot
            ->setIsActive(false)
            ->save();
    }

    /**
     * @param int $idConcreteTimeslot
     * @param int $idBranch
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getConcreteTimeslot(
        int $idConcreteTimeslot,
        int $idBranch
    ): SpyConcreteTimeSlot
    {
        $concreteTimeslot = $this
            ->deliveryAreaQueryContainer
            ->queryConcreteTimeSlot()
            ->filterByIdConcreteTimeSlot(
                $idConcreteTimeslot
            )
            ->useSpyTimeSlotQuery()
                ->filterByFkBranch(
                    $idBranch
                )
            ->endUse()
            ->findOne();

        if ($concreteTimeslot === null) {
            throw new ConcreteTimeSlotNotFoundException(
                sprintf(
                    ConcreteTimeSlotNotFoundException::NOT_FOUND,
                    $idConcreteTimeslot
                )
            );
        }

        return $concreteTimeslot;
    }

    /**
     * @param int $idConcreteTimeSlot
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
