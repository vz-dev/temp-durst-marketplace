<?php
/**
 * Durst - project - InvoiceQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:28
 */

namespace Pyz\Zed\Invoice\Persistence;

use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;

interface InvoiceQueryContainerInterface
{
    /**
     * @param array $idsSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function queryInvoiceRefsForOrderByIds(array $idsSalesOrder): SpySalesOrderQuery;
}
