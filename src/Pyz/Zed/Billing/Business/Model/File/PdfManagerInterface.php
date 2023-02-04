<?php
/**
 * Durst - project - PdfManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:31
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\PdfTransfer;

interface PdfManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return array
     */
    public function getAllInvoicePdfFilePathsForPeriod(BillingPeriodTransfer $billingPeriodTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): PdfTransfer;

    /**
     * {@inheritDoc}
     *
     * @param array $paths
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    public function mergePdfFiles(array $paths, BillingPeriodTransfer $billingPeriodTransfer): string;
}
