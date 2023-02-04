<?php
/**
 * Durst - project - BillingToInvoiceBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:43
 */

namespace Pyz\Zed\Billing\Dependency\Facade;

interface BillingToInvoiceBridgeInterface
{
    /**
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array;

    /**
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string;
}
