<?php
/**
 * Durst - project - OrderImporterInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.05.21
 * Time: 16:54
 */

namespace Pyz\Zed\GraphMasters\Business\Model\Order;


use Generated\Shared\Transfer\QuoteTransfer;

interface OrderImporterInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrder
     * @return void
     */
    public function importOrder(QuoteTransfer $quoteTransfer, int $idSalesOrder);
}
