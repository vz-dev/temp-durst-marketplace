<?php
/**
 * Durst - project - OrderTaxRateTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.08.20
 * Time: 08:34
 */

namespace Pyz\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class OrderTaxRateTotalCalculator implements CalculatorInterface
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
        $calculableObjectTransfer->requireTotals();
        $this->calculateTaxRateTotalForItems($calculableObjectTransfer->getItems());
        $this->calculateTaxRateTotalForExpenses($calculableObjectTransfer->getExpenses());
        $this->createTransfers($calculableObjectTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    protected function createTransfers(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $calculableObjectTransfer->getTotals()->setTaxRateTotals(new ArrayObject([]));

        foreach ($this->taxRateTotal as $taxRate => $taxRateTotal) {
            $taxRateTotalTransfer = (new TaxRateTotalTransfer())
                ->setRate((float)$taxRate)
                ->setAmount((int)round($taxRateTotal));
            $calculableObjectTransfer->getTotals()->addTaxRateTotals($taxRateTotalTransfer);
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
            $this->taxRateTotal[$this->getTaxRateKey($item->getTaxRate())] += $item->getSumTaxAmountFullAggregation() - $item->getTaxAmountAfterCancellation();
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
            if ($expense->getIsNegative() === true) {
                $this->taxRateTotal[$this->getTaxRateKey($expense->getTaxRate())] -= $expense->getSumTaxAmount() - $expense->getTaxAmountAfterCancellation();
                continue;
            }
            $this->taxRateTotal[$this->getTaxRateKey($expense->getTaxRate())] += $expense->getSumTaxAmount() - $expense->getTaxAmountAfterCancellation();
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
