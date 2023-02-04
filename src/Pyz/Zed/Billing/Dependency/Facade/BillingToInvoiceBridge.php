<?php
/**
 * Durst - project - BillingToInvoiceBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:44
 */

namespace Pyz\Zed\Billing\Dependency\Facade;

use Pyz\Zed\Invoice\Business\InvoiceFacadeInterface;

class BillingToInvoiceBridge implements BillingToInvoiceBridgeInterface
{
    /**
     * @var \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface
     */
    protected $invoiceFacade;

    /**
     * BillingToInvoiceBridge constructor.
     *
     * @param \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface $invoiceFacade
     */
    public function __construct(InvoiceFacadeInterface $invoiceFacade)
    {
        $this->invoiceFacade = $invoiceFacade;
    }

    /**
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array
    {
        return $this
            ->invoiceFacade
            ->getInvoiceReferencesForOrderIds($idsSalesOrder);
    }

    /**
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(
        string $invoiceReference,
        int $idBranch
    ): string {
        return $this
            ->invoiceFacade
            ->getInvoicePdfFilePathForOrder(
                $invoiceReference,
                $idBranch
            );
    }
}
