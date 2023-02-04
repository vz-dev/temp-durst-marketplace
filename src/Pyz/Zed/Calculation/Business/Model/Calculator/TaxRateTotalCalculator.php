<?php
/**
 * Durst - project - TaxRateTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.08.20
 * Time: 09:23
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class TaxRateTotalCalculator implements CalculatorInterface
{
    /**
     * @var array
     */
    protected $taxRateTotal = [];

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->taxRateTotal = [];
                $concreteTimeSlot->requireTotals();
                $this->calculateTaxRateTotalForItems($calculableObjectTransfer->getItems());
                $this->calculateTaxRateTotalForExpenses($concreteTimeSlot->getExpenses());
                $this->createTransfers($concreteTimeSlot->getTotals());
            }
            return;
        }
        $calculableObjectTransfer->requireTotals();
        $this->calculateTaxRateTotalForItems($calculableObjectTransfer->getItems());
        $this->calculateTaxRateTotalForExpenses($calculableObjectTransfer->getExpenses());
        $this->createTransfers($calculableObjectTransfer->getTotals());
    }

    /**
     * @param \Generated\Shared\Transfer\TotalsTransfer $totalsTransfer
     *
     * @return void
     */
    protected function createTransfers(TotalsTransfer $totalsTransfer): void
    {
        $totalsTransfer->setTaxRateTotals(new ArrayObject([]));
        foreach ($this->taxRateTotal as $taxRate => $taxRateTotal) {
            $taxRateTotalTransfer = (new TaxRateTotalTransfer())
                ->setRate((float)$taxRate)
                ->setAmount((int)round($taxRateTotal));
            $totalsTransfer->addTaxRateTotals($taxRateTotalTransfer);
        }

        $this->taxRateTotal = [];
    }

    /**
     * @param iterable|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return void
     */
    protected function calculateTaxRateTotalForItems(iterable $items): void
    {
        foreach ($items as $item) {
            $this->prepareTaxRateEntry($item->getTaxRate());
            $this->taxRateTotal[$this->getTaxRateKey($item->getTaxRate())] += $item->getSumTaxAmount();
        }
    }

    /**
     * @param iterable|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     *
     * @return void
     */
    protected function calculateTaxRateTotalForExpenses(iterable $expenses): void
    {
        foreach ($expenses as $expense) {
            $this->prepareTaxRateEntry($expense->getTaxRate());
            $this->taxRateTotal[$this->getTaxRateKey($expense->getTaxRate())] += $expense->getSumTaxAmount();
        }
    }

    /**
     * @param float $taxRate
     *
     * @return void
     */
    protected function prepareTaxRateEntry(float $taxRate): void
    {
        $key = $this->getTaxRateKey($taxRate);
        if (array_key_exists($key, $this->taxRateTotal) !== true) {
            $this->taxRateTotal[$key] = 0;
        }
    }

    /**
     * @param float $taxRate
     *
     * @return string
     */
    protected function getTaxRateKey(float $taxRate): string
    {
        return number_format($taxRate, 2);
    }
}
