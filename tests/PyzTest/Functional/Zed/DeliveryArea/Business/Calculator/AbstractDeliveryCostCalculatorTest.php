<?php
/**
 * Durst - project - AbstractDeliveryCostCalculatorTest.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.09.18
 * Time: 20:12
 */

namespace PyzTest\Functional\Zed\DeliveryArea\Business\Calculator;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;

abstract class AbstractDeliveryCostCalculatorTest extends Unit
{
    public const SUM_PRICE = 3;

    /**
     * @return ExpenseTransfer
     */
    protected function createDummyExpense() : ExpenseTransfer
    {
        return (new ExpenseTransfer())
            ->setType('notDeliveryCostExpenseType')
            ->setSumPrice(5);
    }

    /**
     * @return CalculableObjectTransfer
     */
    protected function createCalculableObjectTransfer() : CalculableObjectTransfer
    {
        return (new CalculableObjectTransfer())
            ->addConcreteTimeSlots(
                $this->createConcreteTimeSlot()
            );
    }

    /**
     * @return TotalsTransfer
     */
    protected function createTotalsTransfer() : TotalsTransfer
    {
        return new TotalsTransfer();
    }

    /**
     * @param string $type
     * @param int $sumPrice
     * @return ExpenseTransfer
     */
    protected function createExpenseTransfer(
        string $type,
        int $sumPrice
    ) : ExpenseTransfer
    {
        return (new ExpenseTransfer())
            ->setType($type)
            ->setSumPrice($sumPrice);
    }

    /**
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    protected function createConcreteTimeSlot(): ConcreteTimeSlotTransfer
    {
        return (new ConcreteTimeSlotTransfer())
            ->setTotals($this->createTotalsTransfer())
            ->addExpenses(
                $this->createExpenseTransfer(
                    DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE,
                    static::SUM_PRICE
                )
            );
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return int
     */
    protected function getDeliveryCostTotalFromCalculableObject(CalculableObjectTransfer $calculableObjectTransfer) : int
    {
        return $calculableObjectTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->getTotals()
            ->getDeliveryCostTotal();
    }
}
