<?php

namespace Pyz\Zed\Billing\Business;

use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\BillingDependencyProvider;
use Pyz\Zed\Billing\Business\Calculator\InvoicesCalculator;
use Pyz\Zed\Billing\Business\Calculator\InvoicesCalculatorInterface;
use Pyz\Zed\Billing\Business\Generator\BillingItemGenerator;
use Pyz\Zed\Billing\Business\Generator\BillingItemGeneratorInterface;
use Pyz\Zed\Billing\Business\Generator\BillingPeriodGenerator;
use Pyz\Zed\Billing\Business\Generator\BillingPeriodGeneratorInterface;
use Pyz\Zed\Billing\Business\Generator\BillingReferenceGenerator;
use Pyz\Zed\Billing\Business\Generator\BillingReferenceGeneratorInterface;
use Pyz\Zed\Billing\Business\Model\BillingItem;
use Pyz\Zed\Billing\Business\Model\BillingItemInterface;
use Pyz\Zed\Billing\Business\Model\BillingPeriod;
use Pyz\Zed\Billing\Business\Model\BillingPeriodInterface;
use Pyz\Zed\Billing\Business\Model\File\CsvManager;
use Pyz\Zed\Billing\Business\Model\File\CsvManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\DatevCsvExport;
use Pyz\Zed\Billing\Business\Model\File\DatevCsvExportInterface;
use Pyz\Zed\Billing\Business\Model\File\DownloadManager;
use Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\PathManager;
use Pyz\Zed\Billing\Business\Model\File\PathManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\PdfManager;
use Pyz\Zed\Billing\Business\Model\File\PdfManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\ZipArchiveManager;
use Pyz\Zed\Billing\Business\Model\File\ZipArchiveManagerInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMoneyBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridgeInterface;
use Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainer;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method BillingConfig getConfig()
 * @method BillingQueryContainer getQueryContainer()
 */
class BillingBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return BillingReferenceGeneratorInterface
     */
    public function createBillingReferenceGenerator(): BillingReferenceGeneratorInterface
    {
        return new BillingReferenceGenerator(
            $this->getConfig(),
            $this->getMerchantFacade(),
            $this->getSequenceNumberFacade()
        );
    }

    /**
     * @return BillingPeriodGeneratorInterface
     */
    public function createBillingPeriodGenerator(): BillingPeriodGeneratorInterface
    {
        return new BillingPeriodGenerator(
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->createBillingPeriod(),
            $this->getMerchantFacade(),
            $this->createBillingReferenceGenerator(),
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @return BillingItemGeneratorInterface
     */
    public function createBillingItemGenerator(): BillingItemGeneratorInterface
    {
        return new BillingItemGenerator(
            $this->getConfig(),
            $this->createBillingPeriod(),
            $this->createBillingItem(),
            $this->createInvoicesCalculator(),
            $this->getSalesFacade()
        );
    }

    /**
     * @return BillingItemInterface
     */
    public function createBillingItem() : BillingItemInterface
    {
        return new BillingItem(
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->createBillingPeriod()
        );
    }

    /**
     * @return BillingPeriodInterface
     */
    public function createBillingPeriod(): BillingPeriodInterface
    {
        return new BillingPeriod(
            $this->getQueryContainer(),
            $this->getMerchantFacade(),
            $this->createDownloadManager()
        );
    }

    /**
     * @return InvoicesCalculatorInterface
     */
    public function createInvoicesCalculator(): InvoicesCalculatorInterface
    {
        return new InvoicesCalculator();
    }

    /**
     * @return DatevCsvExportInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDatevCsvManager(): DatevCsvExportInterface
    {
        return new DatevCsvExport(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getMoneyFacade(),
            $this->getMerchantFacade(),
            $this->getGraphmastersFacade()
        );
    }

    /**
     * @return DownloadManagerInterface
     */
    protected function createDownloadManager(): DownloadManagerInterface
    {
        return new DownloadManager(
            $this->createPdfManager(),
            $this->createZipArchiveManager(),
            $this->createCsvManager()
        );
    }

    /**
     * @return PathManagerInterface
     */
    protected function createPathManager(): PathManagerInterface
    {
        return new PathManager(
            $this->getFileSystem(),
            $this->getConfig()
        );
    }

    /**
     * @return PdfManagerInterface
     */
    protected function createPdfManager(): PdfManagerInterface
    {
        return new PdfManager(
            $this->getPdfFacade(),
            $this->getInvoiceFacade(),
            $this->getSalesFacade(),
            $this->getFileSystem(),
            $this->getConfig()
        );
    }

    /**
     * @return ZipArchiveManagerInterface
     */
    protected function createZipArchiveManager(): ZipArchiveManagerInterface
    {
        return new ZipArchiveManager(
            $this->getConfig(),
            $this->createPathManager()
        );
    }

    /**
     * @return CsvManagerInterface
     */
    protected function createCsvManager(): CsvManagerInterface
    {
        return new CsvManager(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->createPathManager(),
            $this->getMerchantFacade(),
            $this->getGraphmastersFacade()
        );
    }

    /**
     * @return BillingToSequenceNumberBridgeInterface
     */
    protected function getSequenceNumberFacade(): BillingToSequenceNumberBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_SEQUENCE_NUMBER
            );
    }

    /**
     * @return BillingToMerchantBridgeInterface
     */
    protected function getMerchantFacade(): BillingToMerchantBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_MERCHANT
            );
    }

    /**
     * @return BillingToSalesBridgeInterface
     */
    protected function getSalesFacade(): BillingToSalesBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_SALES
            );
    }

    /**
     * @return BillingToMerchantQueryContainerBridgeInterface
     */
    protected function getMerchantQueryContainer() : BillingToMerchantQueryContainerBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::QUERY_MERCHANT
            );
    }

    /**
     * @return BillingToPdfBridgeInterface
     */
    protected function getPdfFacade(): BillingToPdfBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_PDF
            );
    }

    /**
     * @return BillingToInvoiceBridgeInterface
     */
    protected function getInvoiceFacade(): BillingToInvoiceBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_INVOICE
            );
    }

    /**
     * @return Filesystem
     */
    protected function getFileSystem(): Filesystem
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FILE_SYSTEM
            );
    }

    /**
     * @return BillingToMoneyBridgeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMoneyFacade(): BillingToMoneyBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_MONEY
            );
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getGraphmastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                BillingDependencyProvider::FACADE_GRAPHMASTERS
            );
    }
}
