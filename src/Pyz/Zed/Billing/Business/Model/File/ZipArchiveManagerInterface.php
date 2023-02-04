<?php
/**
 * Durst - project - ZipArchiveManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:30
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;

interface ZipArchiveManagerInterface
{
    /**
     * @param array $fileNames
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    public function zipFilesAndGetPath(array $fileNames, BillingPeriodTransfer $billingPeriodTransfer): string;
}
