<?php

namespace Pyz\Zed\Sales\Dependency\Facade;

use Pyz\Zed\Invoice\Business\InvoiceFacadeInterface;

class SalesToInvoiceBridge implements SalesToInvoiceInterface
{
    /**
     * @var InvoiceFacadeInterface
     */
    protected $invoiceFacade;

    /**
     * @param InvoiceFacadeInterface $invoiceFacade
     */
    public function __construct(InvoiceFacadeInterface $invoiceFacade)
    {
        $this->invoiceFacade = $invoiceFacade;
    }

    /**
     * @param string $invoiceReference
     * @param int $idBranch
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string
    {
        return $this
            ->invoiceFacade
            ->getInvoicePdfFilePathForOrder($invoiceReference, $idBranch);
    }
}
