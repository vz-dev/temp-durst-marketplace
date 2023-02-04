<?php


namespace Pyz\Zed\Tax\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Tax\Business\Model\Calculator\TaxAmountAfterCancellationCalculator as SprykerTaxAmountAfterCancellationCalculator;

class TaxAmountAfterCancellationCalculator extends SprykerTaxAmountAfterCancellationCalculator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    protected function calculateOrderExpenseTaxAmountAfterCancellation(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                foreach ($concreteTimeSlot->getExpenses() as $expenseTransfer) {
                    if ($expenseTransfer->getCanceledAmount() === null) {
                        continue;
                    }

                    $expenseTransfer->requireSumPriceToPayAggregation();
                    $expenseTransfer->requireSumTaxAmount();

                    $canceledTaxableAmount = $expenseTransfer->getSumPriceToPayAggregation() - $expenseTransfer->getCanceledAmount();

                    if ($canceledTaxableAmount === null) {
                        $expenseTransfer->setTaxAmountAfterCancellation($expenseTransfer->getSumTaxAmount());
                    }

                    $taxAmount = $this->calculateTaxAmount(
                        $canceledTaxableAmount,
                        $expenseTransfer->getTaxRate(),
                        $calculableObjectTransfer->getPriceMode()
                    );

                    $expenseTransfer->setTaxAmountAfterCancellation($expenseTransfer->getSumTaxAmount() - $taxAmount);
                }
            }
        } else {
            parent::calculateOrderExpenseTaxAmountAfterCancellation($calculableObjectTransfer);
        }
    }
}
