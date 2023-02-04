<?php

namespace PyzTest\Functional\Zed\Billing;


use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Calculator\InvoicesCalculator;
use Pyz\Zed\Billing\Business\Calculator\InvoicesCalculatorInterface;
use Pyz\Zed\Billing\Business\Generator\BillingReferenceGenerator;
use Pyz\Zed\Billing\Business\Generator\BillingReferenceGeneratorInterface;
use Pyz\Zed\Billing\Business\Model\BillingItem;
use Pyz\Zed\Billing\Business\Model\BillingItemInterface;
use Pyz\Zed\Billing\Business\Model\BillingPeriod;
use Pyz\Zed\Billing\Business\Model\BillingPeriodInterface;
use Pyz\Zed\Billing\Business\Model\File\CsvManager;
use Pyz\Zed\Billing\Business\Model\File\CsvManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\DownloadManager;
use Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\PathManager;
use Pyz\Zed\Billing\Business\Model\File\PathManagerInterface;
use Pyz\Zed\Billing\Business\Model\File\PdfManager;
use Pyz\Zed\Billing\Business\Model\File\ZipArchiveManager;
use Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridgeInterface;
use Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridge;
use Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainer;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacade;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Invoice\Business\InvoiceFacade;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\Pdf\Business\PdfFacade;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacade;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacade;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

trait BillingMocksTrait
{
    /**
     * @return BillingItemInterface|MockObject
     */
    protected function createBillingItemMock(): BillingItemInterface
    {
        return $this
            ->getMockBuilder(BillingItem::class)
            ->setConstructorArgs([
                $this->createBillingConfigMock(),
                $this->createBillingQueryContainerMock(),
                $this->createBillingPeriodMock(),
            ])
            ->setMethods([
               'createBillingEntity'
            ])
            ->getMock();
    }

    /**
     * @return BillingConfig|MockObject
     */
    protected function createBillingConfigMock(): BillingConfig
    {
        return $this
            ->getMockBuilder(BillingConfig::class)
            ->getMock();
    }

    /**
     * @return BillingQueryContainerInterface|MockObject
     */
    protected function createBillingQueryContainerMock(): BillingQueryContainerInterface
    {
        return $this
            ->getMockBuilder(BillingQueryContainer::class)
            ->getMock();
    }

    /**
     * @return BillingPeriodInterface|MockObject
     */
    protected function createBillingPeriodMock(): BillingPeriodInterface
    {
        return $this
            ->getMockBuilder(BillingPeriod::class)
            ->setConstructorArgs([
                $this->createBillingQueryContainerMock(),
                $this->createBillingToMerchantMock(),
                $this->createDownloadManagerMock(),
            ])
            ->getMock();
    }

    /**
     * @return BillingToMerchantBridgeInterface|MockObject
     */
    protected function createBillingToMerchantMock(): BillingToMerchantBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToMerchantBridge::class)
            ->setConstructorArgs([
                $this->createMerchantFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return MerchantFacadeInterface|MockObject
     */
    protected function createMerchantFacadeMock(): MerchantFacadeInterface
    {
        return $this
            ->getMockBuilder(MerchantFacade::class)
            ->getMock();
    }

    /**
     * @return DownloadManagerInterface|MockObject
     */
    protected function createDownloadManagerMock(): DownloadManagerInterface
    {
        return $this
            ->getMockBuilder(DownloadManager::class)
            ->setConstructorArgs([
                $this->createPdfManagerMock(),
                $this->createZipArchiveManagerMock(),
                $this->createCsvManagerMock(),
            ])
            ->getMock();
    }

    /**
     * @return PdfManager|MockObject
     */
    protected function createPdfManagerMock(): PdfManager
    {
        return $this
            ->getMockBuilder(PdfManager::class)
            ->setConstructorArgs([
                $this->createBillingToPdfBridgeMock(),
                $this->createBillingToInvoiceBridgeMock(),
                $this->createBillingToSalesBridgeMock(),
                $this->createFilesystemMock(),
                $this->createBillingConfigMock(),
            ])
            ->getMock();
    }

    /**
     * @return BillingToPdfBridgeInterface|MockObject
     */
    protected function createBillingToPdfBridgeMock(): BillingToPdfBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToPdfBridge::class)
            ->setConstructorArgs([
                $this->createPdfFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return PdfFacadeInterface|MockObject
     */
    protected function createPdfFacadeMock(): PdfFacadeInterface
    {
        return $this
            ->getMockBuilder(PdfFacade::class)
            ->getMock();
    }

    /**
     * @return BillingToInvoiceBridgeInterface|MockObject
     */
    protected function createBillingToInvoiceBridgeMock(): BillingToInvoiceBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToInvoiceBridge::class)
            ->setConstructorArgs([
                $this->createInvoiceFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return InvoiceFacade|MockObject
     */
    protected function createInvoiceFacadeMock(): InvoiceFacade
    {
        return $this
            ->getMockBuilder(InvoiceFacade::class)
            ->getMock();
    }

    /**
     * @return BillingToSalesBridgeInterface|MockObject
     */
    protected function createBillingToSalesBridgeMock(): BillingToSalesBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToSalesBridgeInterface::class)
            ->setConstructorArgs([
                $this->createSalesFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return SalesFacadeInterface|MockObject
     */
    protected function createSalesFacadeMock(): SalesFacadeInterface
    {
        return $this
            ->getMockBuilder(SalesFacade::class)
            ->getMock();
    }

    /**
     * @return Filesystem|MockObject
     */
    protected function createFilesystemMock(): Filesystem
    {
        return $this
            ->getMockBuilder(Filesystem::class)
            ->getMock();
    }

    /**
     * @return ZipArchiveManager|MockObject
     */
    protected function createZipArchiveManagerMock(): ZipArchiveManager
    {
        return $this
            ->getMockBuilder(ZipArchiveManager::class)
            ->setConstructorArgs([
                $this->createBillingConfigMock(),
                $this->createPathManagerMock(),
            ])
            ->getMock();
    }

    /**
     * @return PathManagerInterface|MockObject
     */
    protected function createPathManagerMock(): PathManagerInterface
    {
        return $this
            ->getMockBuilder(PathManager::class)
            ->setConstructorArgs([
                $this->createFilesystemMock(),
                $this->createBillingConfigMock(),
            ])
            ->getMock();
    }

    /**
     * @return CsvManagerInterface|MockObject
     */
    protected function createCsvManagerMock(): CsvManagerInterface
    {
        return $this
            ->getMockBuilder(CsvManager::class)
            ->setConstructorArgs([
                $this->createBillingQueryContainerMock(),
                $this->createBillingConfigMock(),
                $this->createPathManagerMock(),
                $this->createBillingToMerchantMock(),
                $this->createGraphMastersFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return InvoicesCalculatorInterface|MockObject
     */
    protected function createInvoiceCalculatorMock(): InvoicesCalculatorInterface
    {
        return $this
            ->getMockBuilder(InvoicesCalculator::class)
            ->getMock();
    }

    /**
     * @return BillingReferenceGeneratorInterface|MockObject
     */
    protected function createBillingReferenceGeneratorMock(): BillingReferenceGeneratorInterface
    {
        return $this
            ->getMockBuilder(BillingReferenceGenerator::class)
            ->setConstructorArgs([
                $this->createBillingConfigMock(),
                $this->createBillingToMerchantBridgeMock(),
                $this->createSequenceNumberFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return BillingToMerchantBridgeInterface|MockObject
     */
    protected function createBillingToMerchantBridgeMock(): BillingToMerchantBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToMerchantBridge::class)
            ->setConstructorArgs([
                $this->createMerchantFacadeMock(),
            ])
            ->getMock();
    }

    /**
     * @return BillingToSequenceNumberBridgeInterface|MockObject
     */
    protected function createSequenceNumberFacadeMock(): BillingToSequenceNumberBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToSequenceNumberBridge::class)
            ->setConstructorArgs([
                $this->createSequenceNumberMock(),
            ])
            ->getMock();
    }

    /**
     * @return SequenceNumberFacadeInterface|MockObject
     */
    protected function createSequenceNumberMock(): SequenceNumberFacadeInterface
    {
        return $this
            ->getMockBuilder(SequenceNumberFacade::class)
            ->getMock();
    }

    /**
     * @return BillingToMerchantQueryContainerBridgeInterface|MockObject
     */
    protected function createBillingToMerchantQueryContainerMock(): BillingToMerchantQueryContainerBridgeInterface
    {
        return $this
            ->getMockBuilder(BillingToMerchantQueryContainerBridge::class)
            ->setConstructorArgs([
                $this->createMerchantQueryContainerMock(),
            ])
            ->setMethods(['queryBranch'])
            ->getMock();
    }

    /**
     * @return MerchantQueryContainerInterface|MockObject
     */
    protected function createMerchantQueryContainerMock(): MerchantQueryContainerInterface
    {
        return $this
            ->getMockBuilder(MerchantQueryContainer::class)
            ->getMock();
    }

    /**
     * @return GraphMastersFacadeInterface|MockObject
     */
    protected function createGraphMastersFacadeMock(): GraphMastersFacadeInterface
    {
        return $this
            ->getMockBuilder(GraphMastersFacade::class)
            ->getMock();
    }
}
