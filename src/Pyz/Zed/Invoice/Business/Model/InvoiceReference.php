<?php
/**
 * Durst - project - InvoiceReference.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:34
 */

namespace Pyz\Zed\Invoice\Business\Model;

use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Pyz\Zed\Invoice\Persistence\InvoiceQueryContainerInterface;

class InvoiceReference implements InvoiceReferenceInterface
{
    /**
     * @var \Pyz\Zed\Invoice\Persistence\InvoiceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * InvoiceReference constructor.
     *
     * @param \Pyz\Zed\Invoice\Persistence\InvoiceQueryContainerInterface $queryContainer
     */
    public function __construct(InvoiceQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $idsSalesOrder
     *
     * @return array
     */
    public function getInvoiceReferencesForOrderIds(array $idsSalesOrder): array
    {
        $results = $this
            ->queryContainer
            ->queryInvoiceRefsForOrderByIds($idsSalesOrder)
            ->find();

        $invoiceReferences = [];
        foreach ($results as $result) {
            $invoiceReferences[$result[SpySalesOrderTableMap::COL_ID_SALES_ORDER]] = $result[SpySalesOrderTableMap::COL_INVOICE_REFERENCE];
        }

        return $invoiceReferences;
    }
}
