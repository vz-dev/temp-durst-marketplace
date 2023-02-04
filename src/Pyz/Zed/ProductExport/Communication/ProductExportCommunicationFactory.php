<?php
/**
 * Durst - project - ProductExportCommunicationFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 12:32
 */

namespace Pyz\Zed\ProductExport\Communication;


use Pyz\Zed\ProductExport\Communication\Table\ProductExportLogTable;
use Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface;
use Pyz\Zed\ProductExport\ProductExportDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * Class ProductExportCommunicationFactory
 * @package Pyz\Zed\ProductExport\Communication
 * @method \Pyz\Zed\ProductExport\ProductExportConfig getConfig()
 */
class ProductExportCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\ProductExport\Communication\Table\ProductExportLogTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductExportLogTable(): ProductExportLogTable
    {
        return new ProductExportLogTable(
            $this->getProductExportQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductExportQueryContainer(): ProductExportQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::QUERY_CONTAINER_PRODUCT_EXPORT
            );
    }
}
