<?php
/**
 * Durst - project - TaxAmountCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 22:01
 */

namespace Pyz\Zed\Tax\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Tax\Business\Model\Calculator\TaxAmountCalculator as SprykerTaxAmountCalculator;

class TaxAmountCalculator extends SprykerTaxAmountCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $priceMode = $calculableObjectTransfer->getPriceMode();

            $haveExpenses = count($calculableObjectTransfer->getExpenses()) > 0;

            $this->calculateTaxSumAmountForItems($calculableObjectTransfer->getItems(), $priceMode, $haveExpenses);
            $this->calculateTaxSumAmountForProductOptions($calculableObjectTransfer->getItems(), $priceMode);

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculateTaxSumAmountForExpenses($concreteTimeSlot->getExpenses(), $priceMode);
            }

            $this->accruedTaxCalculator->resetRoundingErrorDelta();

            $this->calculateTaxUnitAmountForItems($calculableObjectTransfer->getItems(), $priceMode, $haveExpenses);
            $this->calculateTaxUnitAmountForProductOptions($calculableObjectTransfer->getItems(), $priceMode);

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this->calculateTaxUnitAmountForExpenses($concreteTimeSlot->getExpenses(), $priceMode);
            }

            $this->accruedTaxCalculator->resetRoundingErrorDelta();
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     * @param string $priceMode
     *
     * @return void
     */
    protected function calculateTaxSumAmountForExpenses(ArrayObject $expenses, $priceMode)
    {
        $lastExpenseTransfer = null;
        foreach ($expenses as $expenseTransfer) {
            $expenseTransfer->setSumTaxAmount(0);

            if (!$expenseTransfer->getTaxRate()) {
                continue;
            }

            $taxableAmount = $expenseTransfer->getSumPrice() - $expenseTransfer->getSumDiscountAmountAggregation();

            $isNegative = false;

            if ($taxableAmount <= 0) {
                $isNegative = true;
                $taxableAmount = abs($taxableAmount);
            }

            $sumTaxAmount = $this->calculateTaxAmount(
                $taxableAmount,
                $expenseTransfer->getTaxRate(),
                $priceMode,
                static::ROUNDING_ERROR_BUCKET_IDENTIFIER
            );

            if ($isNegative === true) {
                $sumTaxAmount *= -1;
            }

            $expenseTransfer->setSumTaxAmount($sumTaxAmount);
            $lastExpenseTransfer = $expenseTransfer;
        }

        if ($lastExpenseTransfer) {
            $lastExpenseTransfer->setSumTaxAmount(
                (int)round($lastExpenseTransfer->getSumTaxAmount() + $this->accruedTaxCalculator->getRoundingErrorDelta(static::ROUNDING_ERROR_BUCKET_IDENTIFIER))
            );
        }
    }
}
