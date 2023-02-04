<?php
/**
 * Durst - project - OmsToInvoiceBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.03.20
 * Time: 15:36
 */

namespace Pyz\Zed\Oms\Dependency\Facade;

use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Zed\Invoice\Business\InvoiceFacadeInterface;

class OmsToInvoiceBridge implements OmsToInvoiceBridgeInterface
{
    /**
     * @var \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface
     */
    protected $invoiceFacade;

    /**
     * OmsToInvoiceBridge constructor.
     *
     * @param \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface $invoiceFacade
     */
    public function __construct(
        InvoiceFacadeInterface $invoiceFacade
    ) {
        $this->invoiceFacade = $invoiceFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdfFromMailTransfer(MailTransfer $mailTransfer): PdfTransfer
    {
        return $this
            ->invoiceFacade
            ->createInvoicePdfFromMailTransfer($mailTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return string
     */
    public function createInvoiceReference(OrderTransfer $orderTransfer): string
    {
        return $this
            ->invoiceFacade
            ->createInvoiceReference($orderTransfer);
    }
}
