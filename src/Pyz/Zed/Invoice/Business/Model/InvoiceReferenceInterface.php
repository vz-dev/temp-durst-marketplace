<?php
/**
 * Durst - project - InvoiceReferenceInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:34
 */

namespace Pyz\Zed\Invoice\Business\Model;


interface InvoiceReferenceInterface
{
    /**
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array;
}
