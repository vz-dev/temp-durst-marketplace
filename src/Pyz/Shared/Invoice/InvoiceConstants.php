<?php
/**
 * Durst - project - InvoiceConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 10:41
 */

namespace Pyz\Shared\Invoice;


interface InvoiceConstants
{
    // sequence name for invoice needs to be unique for each merchant
    public const INVOICE_REFERENCE_SEQUENCE_NAME_FORMAT = 'INVOICE-%s';
}
