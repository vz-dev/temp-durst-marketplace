<?php

namespace Pyz\Zed\Invoice\Business;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfTransfer;

interface InvoiceFacadeInterface
{
    /**
     * Specification:
     *  - creates a unique invoice reference
     *  - uses a sequence individual to each merchant
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string
     */
    public function createInvoiceReference(OrderTransfer $orderTransfer): string;

    /**
     * Generate an invoice pdf file on disc
     * in order to attach it to the customer mail
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdf(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): PdfTransfer;

    /**
     * Create an invoice PDF from the mail transfer used for the email
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdfFromMailTransfer(
        MailTransfer $mailTransfer
    ): PdfTransfer;

    /**
     * Specification:
     *  - generates the file name with invoice reference and branch id
     *  - prepends the path prefix via the pdf module
     *  - checks whether a file exists with the generated path, throws exception otherwise
     *
     * @see \Pyz\Zed\Invoice\Business\Exception\FileNotFoundException
     *  - returns the generated file path
     *
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string;

    /**
     * Specification:
     *  - returns an array with all the matching invoice references for the given ids
     *  - if a sales order matches an id but doesn't have an invoice reference it will be missing in the result
     *  - the array layout is as follows [
     *       {{idSalesOrder}} => {{invoiceReference}},
     *    ]
     *    e.g. [
     *       1 => 'DST--123123',
 *   *    ]
     *
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array;
}
