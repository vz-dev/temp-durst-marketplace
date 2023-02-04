<?php
/**
 * Durst - project - InvoiceToSalesBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:23
 */

namespace Pyz\Zed\Invoice\Dependency\QueryContainer;

use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;

interface InvoiceToSalesBridgeInterface
{
    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function querySalesOrder(): SpySalesOrderQuery;
}
