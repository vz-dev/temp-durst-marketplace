<?php
/**
 * Durst - project - PriceImportBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 10:55
 */

namespace Pyz\Zed\PriceImport\Business;


use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\PriceImport\Business\Manager\PriceImportManager;
use Pyz\Zed\PriceImport\Business\Manager\PriceImportManagerInterface;
use Pyz\Zed\PriceImport\Business\Mapper\DurstPriceImportMapper;
use Pyz\Zed\PriceImport\Business\Mapper\PriceImportMapperInterface;
use Pyz\Zed\PriceImport\Business\Model\PriceImport;
use Pyz\Zed\PriceImport\Business\Model\PriceImportInterface;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridgeInterface;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridgeInterface;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridgeInterface;
use Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface;
use Pyz\Zed\PriceImport\PriceImportDependencyProvider;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class PriceImportBusinessFactory
 * @package Pyz\Zed\PriceImport\Business
 * @method \Pyz\Zed\PriceImport\PriceImportConfig getConfig()
 */
class PriceImportBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\PriceImport\Business\Model\PriceImportInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceImportModel(): PriceImportInterface
    {
        return new PriceImport(
            $this->getPriceImportQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\PriceImport\Business\Manager\PriceImportManagerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceImportManager(): PriceImportManagerInterface
    {
        return new PriceImportManager(
            $this->getMerchantPriceFacade(),
            $this->getProductFacade(),
            $this->getMailFacade(),
            $this->createPriceImportMappers(),
            $this->getPriceImportQueryContainer(),
            $this->getProductQueryContainer(),
            $this->getConfig(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return PriceImportMapperInterface[]
     */
    protected function createPriceImportMappers(): array
    {
        return [
            $this->createDurstMapper()
        ];
    }

    /**
     * @return \Pyz\Zed\PriceImport\Business\Mapper\PriceImportMapperInterface
     */
    protected function createDurstMapper(): PriceImportMapperInterface
    {
        return new DurstPriceImportMapper();
    }

    /**
     * @return \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantPriceFacade(): PriceImportToMerchantPriceBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::FACADE_MERCHANT_PRICE
            );
    }

    /**
     * @return \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductFacade(): PriceImportToProductBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::FACADE_PRODUCT
            );
    }

    /**
     * @return \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailFacade(): PriceImportToMailBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::FACADE_MAIL
            );
    }

    /**
     * @return \Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPriceImportQueryContainer(): PriceImportQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::QUERY_CONTAINER_PRICE_IMPORT
            );
    }

    /**
     * @return \Pyz\Zed\PRoduct\Persistence\ProductQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductQueryContainer(): ProductQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::QUERY_CONTAINER_PRODUCT
            );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                PriceImportDependencyProvider::FACADE_MERCHANT
            );
    }
}
