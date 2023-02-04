<?php
/**
 * Durst - project - BillingToPdfBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:52
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\PdfTransfer;

interface BillingToPdfBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfFile(PdfTransfer $pdfTransfer): PdfTransfer;
}
