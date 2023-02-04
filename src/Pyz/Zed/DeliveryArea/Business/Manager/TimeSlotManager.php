<?php
/**
 * Durst - project - TimeSlotManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.05.18
 * Time: 16:51
 */

namespace Pyz\Zed\DeliveryArea\Business\Manager;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;

class TimeSlotManager
{
    /**
     * @var \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * TimeSlotManager constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface $queryContainer
     */
    public function __construct(DeliveryAreaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItemsByDeliveryCost(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        $concreteTimeSlotTransfers = $cartChangeTransfer
            ->getQuote()
            ->getConcreteTimeSlots();

        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {
            $deliveryCost = $this
                ->findDeliveryCostByConcreteTimeSlot($concreteTimeSlotTransfer);

            $this->expandExpenses($concreteTimeSlotTransfer, $deliveryCost);
        }

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @param int $deliveryCost
     * @return void
     */
    protected function expandExpenses(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer, int $deliveryCost): void
    {
        $expense = (new ExpenseTransfer())
            ->setType(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE)
            ->setQuantity(1)
            ->setUnitGrossPrice($deliveryCost)
            ->setSumGrossPrice($deliveryCost)
            ->setSumPrice(0)
            ->setName(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_NAME);

        $concreteTimeSlotTransfer
            ->addExpenses($expense);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException
     *
     * @return int
     */
    protected function findDeliveryCostByConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): int
    {
        if ($concreteTimeSlotTransfer->getDeliveryCosts() !== null) {
            return $concreteTimeSlotTransfer->getDeliveryCosts();
        }

        $timeSlotEntity = $this
            ->queryContainer
            ->queryTimeSlotById($concreteTimeSlotTransfer->getFkTimeSlot())
            ->findOne();

        if ($timeSlotEntity === null) {
            throw new TimeSlotNotFoundException(
                sprintf(
                    TimeSlotNotFoundException::NOT_FOUND,
                    $concreteTimeSlotTransfer->getFkTimeSlot()
                )
            );
        }

        $deliveryCost = 0;

        if ($timeSlotEntity->getDeliveryCosts() !== null) {
            $deliveryCost = $timeSlotEntity->getDeliveryCosts();
        }

        return $deliveryCost;
    }
}
