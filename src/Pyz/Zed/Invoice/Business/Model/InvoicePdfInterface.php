<?php
/**
 * Durst - project - InvoicePdfInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 15:14
 */

namespace Pyz\Zed\Invoice\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfTransfer;

interface InvoicePdfInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdf(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): PdfTransfer;

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdfFromMailTransfer(
        MailTransfer $mailTransfer
    ): PdfTransfer;

    /**
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string;
}
