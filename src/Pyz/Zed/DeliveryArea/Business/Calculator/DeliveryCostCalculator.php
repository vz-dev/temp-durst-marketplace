<?php
/**
 * Durst - project - DeliveryCostCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 10:38
 */

namespace Pyz\Zed\DeliveryArea\Business\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class DeliveryCostCalculator implements CalculatorInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->getTotals()
                    ->setDeliveryCostTotal(
                        $this->calculateDeliveryCost($concreteTimeSlot->getExpenses())
                    );
            }
        } else {
            $calculableObjectTransfer
                ->getTotals()
                ->setDeliveryCostTotal(
                    $this->calculateDeliveryCost($calculableObjectTransfer->getExpenses())
                );
        }
    }

    /**
     * @param \ArrayObject|ExpenseTransfer[] $expenses
     * @return int
     */
    protected function calculateDeliveryCost($expenses) : int
    {
        $deliveryCostTotal = 0;
        foreach ($expenses as $expense){
            if ($expense->getType() !== DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE){
                continue;
            }

            $deliveryCostTotal += $expense->getSumPrice();
        }

        return $deliveryCostTotal;
    }
}
