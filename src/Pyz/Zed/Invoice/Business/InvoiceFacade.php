<?php

namespace Pyz\Zed\Invoice\Business;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\Invoice\Business\InvoiceBusinessFactory getFactory()
 */
class InvoiceFacade extends AbstractFacade implements InvoiceFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return string
     */
    public function createInvoiceReference(OrderTransfer $orderTransfer): string
    {
        return $this
            ->getFactory()
            ->createInvoiceReferenceGenerator()
            ->createInvoiceReference($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdf(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): PdfTransfer {
        return $this
            ->getFactory()
            ->createInvoicePdfModel()
            ->createInvoicePdf(
                $orderTransfer,
                $branchTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdfFromMailTransfer(
        MailTransfer $mailTransfer
    ): PdfTransfer {
        return $this
            ->getFactory()
            ->createInvoicePdfModel()
            ->createInvoicePdfFromMailTransfer(
                $mailTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array
    {
        return $this
            ->getFactory()
            ->createInvoiceReferenceModel()
            ->getInvoiceReferencesForOrderIds($idsSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string
    {
        return $this
            ->getFactory()
            ->createInvoicePdfModel()
            ->getInvoicePdfFilePathForOrder($invoiceReference, $idBranch);
    }
}
