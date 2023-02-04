<?php
/**
 * Durst - project - DownloadManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:32
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;

interface DownloadManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    public function prepareDownload(BillingPeriodTransfer $billingPeriodTransfer): string;
}
