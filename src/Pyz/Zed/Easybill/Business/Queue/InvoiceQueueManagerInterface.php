<?php
/**
 * Durst - project - InvoiceQueueManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:35
 */

namespace Pyz\Zed\Easybill\Business\Queue;

interface InvoiceQueueManagerInterface
{
    /**
     * @param string $reference
     */
    public function addReferenceToInvoiceQueue(string $reference): void;
}
