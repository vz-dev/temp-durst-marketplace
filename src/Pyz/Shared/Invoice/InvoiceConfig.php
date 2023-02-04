<?php
/**
 * Durst - project - InvoiceConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 10:39
 */

namespace Pyz\Shared\Invoice;


interface InvoiceConfig
{
    // references for all merchants start with this string
    public const INVOICE_REFERENCE_PREFIX = 'INVOICE_REFERENCE_PREFIX';

    // different parts of invoice reference are separated by this
    public const INVOICE_REFERENCE_SEPARATOR = 'INVOICE_REFERENCE_SEPARATOR';
}
