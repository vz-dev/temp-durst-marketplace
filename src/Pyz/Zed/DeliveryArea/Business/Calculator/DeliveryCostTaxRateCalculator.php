<?php
/**
 * Durst - project - DeliveryCostTaxRateCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.06.18
 * Time: 16:29
 */

namespace Pyz\Zed\DeliveryArea\Business\Calculator;

use DateTime;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;

class DeliveryCostTaxRateCalculator
{
    /**
     * @var \Pyz\Zed\Tax\Business\TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * DeliveryCostTaxRateCalculator constructor.
     *
     * @param \Pyz\Zed\Tax\Business\TaxFacadeInterface $taxFacade
     */
    public function __construct(TaxFacadeInterface $taxFacade)
    {
        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            /** @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $firstTimeSlot */
            $firstTimeSlot = $calculableObjectTransfer->getConcreteTimeSlots()->offsetGet(0);
            $taxRate = $this->getTaxRate(new DateTime($firstTimeSlot->getStartTime()));
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->setQuoteExpenseTaxRate($concreteTimeSlot->getExpenses(), $taxRate);
            }

            return;
        }

        $taxRate = $this->getTaxRate(new DateTime('now'));
        $this->setQuoteExpenseTaxRate($calculableObjectTransfer->getExpenses(), $taxRate);
    }

    /**
     * @param \DateTime $date
     *
     * @return float
     */
    protected function getTaxRate(DateTime $date) : float
    {
        return $this->taxFacade->getDefaultTaxRateForDate($date);
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     * @param $taxRate
     *
     * @return void
     */
    protected function setQuoteExpenseTaxRate($expenses, $taxRate)
    {
        foreach ($expenses as $expense) {
            if ($expense->getType() === DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE) {
                $expense->setTaxRate($taxRate);
            }
        }
    }
}
