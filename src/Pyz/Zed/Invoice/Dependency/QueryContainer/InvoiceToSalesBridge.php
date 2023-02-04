<?php
/**
 * Durst - project - InvoiceToSalesBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:23
 */

namespace Pyz\Zed\Invoice\Dependency\QueryContainer;

use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;

class InvoiceToSalesBridge implements InvoiceToSalesBridgeInterface
{
    /**
     * @var \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * InvoiceToSalesBridge constructor.
     *
     * @param \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(SalesQueryContainerInterface $salesQueryContainer)
    {
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function querySalesOrder(): SpySalesOrderQuery
    {
        return $this
            ->salesQueryContainer
            ->querySalesOrder();
    }
}
