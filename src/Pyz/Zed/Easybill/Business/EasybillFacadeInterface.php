<?php

namespace Pyz\Zed\Easybill\Business;

interface EasybillFacadeInterface
{
    /**
     * Specification:
     *  - creates document with type invoice in easybill
     *  - returns true only if the invoice was created successfully and the order was updated with
     *      - the invoice reference
     *      - the timestamp when the invoice was created
     *      - the url of the saved invoice pdf file
     *  - throws @see \Pyz\Zed\Easybill\Business\Exception\EasybillException if an error occurs
     *
     * @return bool
     */
    public function createInvoice(): bool;

    /**
     * Specification:
     *  - adds the reference to the invoice queue to be consumed in chunks
     *    (the easybill api only supports 60 requests per minute)
     *
     * @param string $reference
     */
    public function addReferenceToInvoiceQueue(string $reference): void;
}
