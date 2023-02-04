<?php
/**
 * Durst - project - ProductExportBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:39
 */

namespace Pyz\Zed\ProductExport\Business;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\ProductExport\Business\Manager\ExportManager;
use Pyz\Zed\ProductExport\Business\Manager\ExportManagerInterface;
use Pyz\Zed\ProductExport\Business\Model\ProductExport;
use Pyz\Zed\ProductExport\Business\Model\ProductExportInterface;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMailBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMerchantPriceBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToProductBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Persistence\ProductExportToProductQueryContainerBridgeInterface;
use Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface;
use Pyz\Zed\ProductExport\ProductExportDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProductExportBusinessFactory
 * @package Pyz\Zed\ProductExport\Business
 * @method \Pyz\Zed\ProductExport\ProductExportConfig getConfig()
 * @method \Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainer getQueryContainer()
 */
class ProductExportBusinessFactory extends AbstractBusinessFactory
{
    /**
     *
     * @return \Pyz\Zed\ProductExport\Business\Model\ProductExportInterface
     */
    public function createProductExportModel(): ProductExportInterface
    {
        return new ProductExport(
            $this->getProductExportQueryContainer()
        );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Business\Manager\ExportManagerInterface
     */
    public function createProductExportManager(): ExportManagerInterface
    {
        return new ExportManager(
            $this->getProductExportQueryContainer(),
            $this->getProductQueryContainer(),
            $this->getProductFacade(),
            $this->getMerchantPriceFacade(),
            $this->getMailFacade(),
            $this->getConfig(),
            $this->createFilesystem(),
            $this->getMerchantFacade()
        );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMerchantPriceBridgeInterface
     */
    protected function getMerchantPriceFacade(): ProductExportToMerchantPriceBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::FACADE_MERCHANT_PRICE
            );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToProductBridgeInterface
     */
    protected function getProductFacade(): ProductExportToProductBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::FACADE_PRODUCT
            );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMailBridgeInterface
     */
    protected function getMailFacade(): ProductExportToMailBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::FACADE_MAIL
            );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface
     */
    protected function getProductExportQueryContainer(): ProductExportQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::QUERY_CONTAINER_PRODUCT_EXPORT
            );
    }

    /**
     *
     * @return \Pyz\Zed\ProductExport\Dependency\Persistence\ProductExportToProductQueryContainerBridgeInterface
     */
    protected function getProductQueryContainer(): ProductExportToProductQueryContainerBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::QUERY_CONTAINER_PRODUCT
            );
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                ProductExportDependencyProvider::FACADE_MERCHANT
            );
    }
}
