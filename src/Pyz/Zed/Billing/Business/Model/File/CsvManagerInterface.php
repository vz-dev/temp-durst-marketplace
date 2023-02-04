<?php
/**
 * Durst - project - CsvManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.05.20
 * Time: 16:24
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;

interface CsvManagerInterface
{
    /**
     * @param BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    public function createCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string;
}
