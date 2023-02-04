<?php

namespace Pyz\Zed\MerchantPrice\Business;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\MerchantPrice\Business\Calculator\MerchantPriceCalculator;
use Pyz\Zed\MerchantPrice\Business\Manager\PriceManager;
use Pyz\Zed\MerchantPrice\Business\Map\PriceDataPageMapBuilder;
use Pyz\Zed\MerchantPrice\Business\Model\CampaignPrice;
use Pyz\Zed\MerchantPrice\Business\Model\Catalog;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculator;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface;
use Pyz\Zed\MerchantPrice\Business\Model\Item;
use Pyz\Zed\MerchantPrice\Business\Model\Price;
use Pyz\Zed\MerchantPrice\Business\Model\PriceExport;
use Pyz\Zed\MerchantPrice\Business\Model\PriceImport;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceSavePluginInterface;
use Pyz\Zed\MerchantPrice\MerchantPriceDependencyProvider;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Price\Business\PriceFacadeInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;

/**
 * @method \Pyz\Zed\MerchantPrice\MerchantPriceConfig getConfig()
 * @method \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainer getQueryContainer()
 */
class MerchantPriceBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return Price
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceModel()
    {
        return new Price(
            $this->getQueryContainer(),
            $this->getProductFacade(),
            $this->getMerchantFacade()->getCurrentBranch()->getIdBranch(),
            $this->createTaxAmountCalculator(),
            $this->getPostMerchantPriceSavePlugins(),
            $this->getPostMerchantPriceDeletePlugins()
        );
    }

    /**
     * @return Item
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createItemModel()
    {
        return new Item(
            $this->getQueryContainer(),
            $this->getProductFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\MerchantPrice\Business\Model\PriceExport
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceExportModel(): PriceExport
    {
        return new PriceExport(
            $this->getQueryContainer(),
            $this->getProductFacade()
        );
    }

    /**
     * @return \Pyz\Zed\MerchantPrice\Business\Model\PriceImport
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceImportModel(): PriceImport
    {
        return new PriceImport(
            $this->getQueryContainer(),
            $this->createTaxAmountCalculator(),
            $this->getPostMerchantPriceDeletePlugins()
        );
    }

    /**
     * @return PriceManager
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceManager() : PriceManager
    {
        return new PriceManager(
            $this->getQueryContainer(),
            $this->getPriceFacade()
        );
    }

    /**
     * @return Catalog
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCatalog() : Catalog
    {
        return new Catalog(
            $this->getQueryContainer(),
            $this->getUtilEncodingService(),
            $this->getLocaleFacade()->getCurrentLocale(),
            $this->createTaxAmountCalculator()
        );
    }

    /**
     * @return MerchantPriceCalculator
     */
    public function createMerchantPriceCalculator() : MerchantPriceCalculator
    {
        return new MerchantPriceCalculator();
    }

    /**
     * @return \Pyz\Zed\MerchantPrice\Business\Model\CampaignPrice
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPriceModel(): CampaignPrice
    {
        return new CampaignPrice(
            $this
                ->getQueryContainer(),
            $this
                ->getProductFacade(),
            $this
                ->createTaxAmountCalculator()
        );
    }

    /**
     * @return \Pyz\Zed\Product\Business\ProductFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductFacade()
    {
        return $this->getProvidedDependency(MerchantPriceDependencyProvider::FACADE_PRODUCT);
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(MerchantPriceDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return PriceFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPriceFacade() : PriceFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::FACADE_PRICE);
    }

    /**
     * @return UtilEncodingServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getUtilEncodingService() : UtilEncodingServiceInterface
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return LocaleFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getLocaleFacade() : LocaleFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return TaxFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTaxFacade() : TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::FACADE_TAX);
    }

    /**
     * @return TaxAmountCalculatorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createTaxAmountCalculator() : TaxAmountCalculatorInterface
    {
        return new TaxAmountCalculator(
            $this->getTaxFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return PriceDataPageMapBuilder
     */
    public function createPriceDataPageMapBuilder() : PriceDataPageMapBuilder
    {
        return new PriceDataPageMapBuilder();
    }

    /**
     * @return PostMerchantPriceSavePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPostMerchantPriceSavePlugins() : array
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::POST_MERCHANT_PRICE_SAVE_PLUGINS);
    }

    /**
     * @return PostMerchantPriceDeletePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPostMerchantPriceDeletePlugins()  : array
    {
        return $this
            ->getProvidedDependency(MerchantPriceDependencyProvider::POST_MERCHANT_PRICE_DELETE_PLUGINS);
    }
}
