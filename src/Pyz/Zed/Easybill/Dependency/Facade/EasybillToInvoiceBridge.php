<?php
/**
 * Durst - project - EasybillToInvoiceBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:19
 */

namespace Pyz\Zed\Easybill\Dependency\Facade;

use Pyz\Zed\Invoice\Business\InvoiceFacadeInterface;

class EasybillToInvoiceBridge implements EasybillToInvoiceBridgeInterface
{
    /**
     * @var \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface
     */
    protected $invoiceFacade;

    /**
     * EasybillToInvoiceBridge constructor.
     *
     * @param \Pyz\Zed\Invoice\Business\InvoiceFacadeInterface $invoiceFacade
     */
    public function __construct(InvoiceFacadeInterface $invoiceFacade)
    {
        $this->invoiceFacade = $invoiceFacade;
    }
}
