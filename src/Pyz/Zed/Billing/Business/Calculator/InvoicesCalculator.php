<?php
/**
 * Durst - project - InvoicesCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.02.20
 * Time: 09:54
 */

namespace Pyz\Zed\Billing\Business\Calculator;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;

class InvoicesCalculator implements InvoicesCalculatorInterface
{


    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    public function calculateTotals(BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $totalAmount = 0;
        $totalTaxAmount = 0;
        $totalReturnDepositAmount = 0;
        $totalDiscountAmount = 0;
        $totalVoucherDiscountAmount = 0;
        $taxRateTotalAmount = [];
        foreach ($billingPeriodTransfer->getBillingItems() as $billingItemTransfer) {
            $totalAmount += $billingItemTransfer->getAmount();
            $totalTaxAmount += $billingItemTransfer->getTaxAmount();
            $totalReturnDepositAmount += $billingItemTransfer->getReturnDepositAmount();
            $totalDiscountAmount += $billingItemTransfer->getDiscountAmount();
            $totalVoucherDiscountAmount += $billingItemTransfer->getVoucherDiscountAmount();
            foreach ($billingItemTransfer->getTaxRateTotals() as $taxRateTotal) {
                $key = number_format($taxRateTotal->getRate(), 2);
                if(array_key_exists($key, $taxRateTotalAmount) !== true){
                    $taxRateTotalAmount[$key] = 0;
                }

                $taxRateTotalAmount[$key] += $taxRateTotal->getAmount();
            }
        }

        $billingPeriodTransfer
            ->setTotalAmount($totalAmount)
            ->setTotalTaxAmount($totalTaxAmount)
            ->setTotalReturnDepositAmount($totalReturnDepositAmount)
            ->setTotalDiscountAmount($totalDiscountAmount)
            ->setTotalVoucherDiscountAmount($totalVoucherDiscountAmount);

        foreach ($taxRateTotalAmount as $taxRate => $taxAmount) {
            foreach($billingPeriodTransfer->getTaxRateTotals() as $taxRateTotal) {
                if ((float) $taxRateTotal->getRate() === (float)$taxRate) {
                    $taxRateTotal->setAmount($taxRateTotal->getAmount() + $taxAmount);

                    continue 2;
                }
            }

            $billingPeriodTransfer->addTaxRateTotals(
                (new TaxRateTotalTransfer())
                    ->setRate((float)$taxRate)
                    ->setAmount($taxAmount)
            );
        }
    }
}
