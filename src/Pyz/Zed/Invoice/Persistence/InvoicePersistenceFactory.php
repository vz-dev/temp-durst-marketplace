<?php
/**
 * Durst - project - InvoicePersistenceFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 09:28
 */

namespace Pyz\Zed\Invoice\Persistence;

use Pyz\Zed\Invoice\Dependency\QueryContainer\InvoiceToSalesBridgeInterface;
use Pyz\Zed\Invoice\InvoiceDependencyProvider;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\Invoice\InvoiceConfig getConfig()
 * @method \Pyz\Zed\Invoice\Persistence\InvoiceQueryContainer getQueryContainer()
 */
class InvoicePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Pyz\Zed\Invoice\Dependency\QueryContainer\InvoiceToSalesBridgeInterface
     */
    public function getSalesQueryContainer(): InvoiceToSalesBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                InvoiceDependencyProvider::QUERY_CONTAINER_SALES
            );
    }
}
