<?php


namespace Pyz\Zed\Calculation\Business\Model\Aggregator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Shared\Discount\DiscountConstants;
use Spryker\Zed\Calculation\Business\Model\Aggregator\DiscountAmountAggregator as SprykerDiscountAmountAggregator;

class DiscountAmountAggregator extends SprykerDiscountAmountAggregator
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $this
                ->calculateDiscountAmountAggregationForItems($calculableObjectTransfer->getItems());

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $this
                    ->calculateDiscountAmountAggregationForExpenses($concreteTimeSlot->getExpenses());
            }

            $this
                ->updateDiscountTotals($calculableObjectTransfer);
        } else {
            parent::recalculate($calculableObjectTransfer);
        }
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenses
     *
     * @return void
     */
    protected function calculateDiscountAmountAggregationForExpenses(ArrayObject $expenses)
    {
        foreach ($expenses as $expenseTransfer) {
            $calculatedDiscounts = $expenseTransfer
                ->getCalculatedDiscounts();

            $discountType = null;
            $firstDiscount = null;

            if ($calculatedDiscounts->count() > 0) {
                /* @var $firstDiscount \Generated\Shared\Transfer\CalculatedDiscountTransfer */
                $firstDiscount = $calculatedDiscounts
                    ->offsetGet(
                        0
                    );

                $discountType = $expenseTransfer
                    ->getType();
            }

            $maxUnitDiscountAmount = $expenseTransfer
                ->getUnitPrice();

            if ($firstDiscount !== null && $discountType === DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                $maxUnitDiscountAmount = $firstDiscount
                    ->getUnitAmount();
            }

            $expenseTransfer->setUnitDiscountAmountAggregation(
                $this->calculateUnitDiscountAmountAggregation(
                    $calculatedDiscounts,
                    $maxUnitDiscountAmount
                )
            );

            $maxSumDiscountAmount = $expenseTransfer
                ->getSumPrice();

            if ($firstDiscount !== null && $discountType === DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                $maxSumDiscountAmount = $firstDiscount
                    ->getSumAmount();
            }

            $sumDiscountAmountAggregation = $this->calculateSumDiscountAmountAggregation(
                $calculatedDiscounts,
                $maxSumDiscountAmount
            );

            $expenseTransfer->setSumDiscountAmountAggregation($sumDiscountAmountAggregation);
        }
    }
}
