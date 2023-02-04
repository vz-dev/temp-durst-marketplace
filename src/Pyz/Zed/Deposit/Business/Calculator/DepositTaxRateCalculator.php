<?php
/**
 * Durst - project - DepositTaxRateCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.06.18
 * Time: 16:41
 */

namespace Pyz\Zed\Deposit\Business\Calculator;

use DateTime;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Shared\Deposit\DepositConstants;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;

class DepositTaxRateCalculator
{
    /**
     * @var \Pyz\Zed\Tax\Business\TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * DepositTaxRateCalculator constructor.
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
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     * @param $taxRate
     *
     * @return void
     */
    protected function setQuoteExpenseTaxRate($expenses, $taxRate)
    {
        foreach ($expenses as $expense) {
            if (substr($expense->getType(), 0, strlen(DepositConstants::DEPOSIT_EXPENSE_TYPE)) === DepositConstants::DEPOSIT_EXPENSE_TYPE) {
                $expense->setTaxRate($taxRate);
            }
        }
    }
}
