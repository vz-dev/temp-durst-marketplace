<?php

namespace Pyz\Zed\Sales\Dependency\Facade;

interface SalesToInvoiceInterface
{
    /**
     * @param string $invoiceReference
     * @param int $idBranch
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string;
}
