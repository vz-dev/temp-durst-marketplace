<?php
/**
 * Durst - project - InvoicesCalculatorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.02.20
 * Time: 09:54
 */

namespace Pyz\Zed\Billing\Business\Calculator;


use Generated\Shared\Transfer\BillingPeriodTransfer;

interface InvoicesCalculatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    public function calculateTotals(BillingPeriodTransfer $billingPeriodTransfer): void;
}
