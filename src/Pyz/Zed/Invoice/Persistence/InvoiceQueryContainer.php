<?php
/**
 * Durst - project - InvoiceQueryContainer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:29
 */

namespace Pyz\Zed\Invoice\Persistence;

use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class InvoiceQueryContainer
 * @package Pyz\Zed\Invoice\Persistence
 * @method \Pyz\Zed\Invoice\Persistence\InvoicePersistenceFactory getFactory()
 */
class InvoiceQueryContainer extends AbstractQueryContainer implements InvoiceQueryContainerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array $idsSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function queryInvoiceRefsForOrderByIds(array $idsSalesOrder): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->select([
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
                SpySalesOrderTableMap::COL_INVOICE_REFERENCE,
                SpySalesOrderTableMap::COL_FK_BRANCH,
            ])
            ->filterByIdSalesOrder_In($idsSalesOrder)
            ->filterByInvoiceReference(null, Criteria::ISNOTNULL);
    }
}
