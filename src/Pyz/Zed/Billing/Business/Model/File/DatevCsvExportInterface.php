<?php
/**
 * Durst - project - DatevCsvExportInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 13:58
 */

namespace Pyz\Zed\Billing\Business\Model\File;


use Generated\Shared\Transfer\BillingPeriodTransfer;

interface DatevCsvExportInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    public function createDatevCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string;
}
