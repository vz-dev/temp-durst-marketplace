<?php

namespace Pyz\Zed\Invoice\Business;

use Pyz\Zed\Invoice\Business\Generator\InvoiceReferenceGenerator;
use Pyz\Zed\Invoice\Business\Generator\InvoiceReferenceGeneratorInterface;
use Pyz\Zed\Invoice\Business\Model\InvoicePdf;
use Pyz\Zed\Invoice\Business\Model\InvoicePdfInterface;
use Pyz\Zed\Invoice\Business\Model\InvoiceReference;
use Pyz\Zed\Invoice\Business\Model\InvoiceReferenceInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface;
use Pyz\Zed\Invoice\InvoiceDependencyProvider;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method \Pyz\Zed\Invoice\InvoiceConfig getConfig()
 * @method \Pyz\Zed\Invoice\Persistence\InvoiceQueryContainer getQueryContainer()
 */
class InvoiceBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Invoice\Business\Generator\InvoiceReferenceGeneratorInterface
     */
    public function createInvoiceReferenceGenerator(): InvoiceReferenceGeneratorInterface
    {
        return new InvoiceReferenceGenerator(
            $this->getConfig(),
            $this->getMerchantFacade(),
            $this->getSequenceNumberFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Invoice\Business\Model\InvoicePdfInterface
     */
    public function createInvoicePdfModel(): InvoicePdfInterface
    {
        return new InvoicePdf(
            $this->getConfig(),
            $this->getPdfFacade(),
            $this->getHeidelpayRestFacade(),
            $this->getOmsFacade(),
            $this->getFileSystem()
        );
    }

    /**
     * @return \Pyz\Zed\Invoice\Business\Model\InvoiceReferenceInterface
     */
    public function createInvoiceReferenceModel(): InvoiceReferenceInterface
    {
        return new InvoiceReference(
            $this->getQueryContainer()
        );
    }

    /**
     *
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function getFileSystem(): Filesystem
    {
        return $this
            ->getProvidedDependency(
                InvoiceDependencyProvider::FILE_SYSTEM
            );
    }

    /**
     * @return \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface
     */
    protected function getSequenceNumberFacade(): InvoiceToSequenceNumberBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                InvoiceDependencyProvider::FACADE_SEQUENCE_NUMBER
            );
    }

    /**
     * @return \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface
     */
    protected function getMerchantFacade(): InvoiceToMerchantBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                InvoiceDependencyProvider::FACADE_MERCHANT
            );
    }

    /**
     * @return \Pyz\Zed\Pdf\Business\PdfFacadeInterface
     */
    protected function getPdfFacade(): PdfFacadeInterface
    {
        return $this
            ->getProvidedDependency(InvoiceDependencyProvider::FACADE_PDF);
    }

    /**
     * @return \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridgeInterface
     */
    protected function getHeidelpayRestFacade(): InvoiceToHeidelpayRestBridgeInterface
    {
        return $this
            ->getProvidedDependency(InvoiceDependencyProvider::FACADE_HEIDELPAY_REST);
    }

    /**
     * @return \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridgeInterface
     */
    protected function getOmsFacade(): InvoiceToOmsBridgeInterface
    {
        return $this
            ->getProvidedDependency(InvoiceDependencyProvider::FACADE_OMS);
    }
}
