<?php
/**
 * Durst - project - PdfManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:30
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use iio\libmergepdf\Merger;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface;
use Symfony\Component\Filesystem\Filesystem;

class PdfManager implements PdfManagerInterface
{
    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridgeInterface
     */
    protected $pdfFacade;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridgeInterface
     */
    protected $invoiceFacade;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * PdfManager constructor.
     *
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridgeInterface $pdfFacade
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridgeInterface $invoiceFacade
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface $salesFacade
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     * @param \Pyz\Zed\Billing\BillingConfig $config
     */
    public function __construct(
        BillingToPdfBridgeInterface $pdfFacade,
        BillingToInvoiceBridgeInterface $invoiceFacade,
        BillingToSalesBridgeInterface $salesFacade,
        Filesystem $fileSystem,
        BillingConfig $config
    ) {
        $this->pdfFacade = $pdfFacade;
        $this->invoiceFacade = $invoiceFacade;
        $this->salesFacade = $salesFacade;
        $this->fileSystem = $fileSystem;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return array
     */
    public function getAllInvoicePdfFilePathsForPeriod(BillingPeriodTransfer $billingPeriodTransfer): array
    {
        $invoicePdfFilePaths = [];
        foreach ($this->getInvoiceRefs($billingPeriodTransfer) as $idSalesOrder => $invoiceRef) {
            $invoicePdfFilePaths[] = $this
                ->invoiceFacade
                ->getInvoicePdfFilePathForOrder(
                    $invoiceRef,
                    $billingPeriodTransfer->getBranch()->getIdBranch()
                );
        }

        return $invoicePdfFilePaths;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $paths
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    public function mergePdfFiles(array $paths, BillingPeriodTransfer $billingPeriodTransfer): string
    {
        $merger = new Merger();
        $merger->addIterator($paths);
        $fileName = $this->getMergedPdfFileName($billingPeriodTransfer);
        file_put_contents($fileName, $merger->merge());

        return $fileName;
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): PdfTransfer
    {
        $pdfTransfer = $this->createPdfTransfer($billingPeriodTransfer);
        return $this
            ->pdfFacade
            ->createPdfFile($pdfTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function createPdfTransfer(BillingPeriodTransfer $billingPeriodTransfer): PdfTransfer
    {
        return (new PdfTransfer())
            ->setTemplateVariables(
                [
                    'durst' => $this->config->getDurstCompanyTransfer(),
                    'billingPeriod' => $billingPeriodTransfer,
                    'baseUrl' => $this->config->getBaseUrl(),
                ]
            )
            ->setTemplate(
                $this
                    ->config
                    ->getBillingInvoiceMailTemplate()
            )
            ->setFileName($this->getPdfFileName($billingPeriodTransfer))
            ->setPdfName($this->getPdfFileName($billingPeriodTransfer))
            ->setDisplayName($this->getPdfFileName($billingPeriodTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    protected function getPdfName(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        return sprintf(
            'Rechnungssummen für Abrechnungszeitraum %s',
            $billingPeriodTransfer->getBillingReference()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    protected function getPdfFileName(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        return sprintf(
            'invoices-%d-%s.pdf',
            $billingPeriodTransfer->getBranch()->getIdBranch(),
            $billingPeriodTransfer->getBillingReference()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    protected function getMergedPdfFileName(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        return sprintf(
            'invoices-merged-%d-%s.pdf',
            $billingPeriodTransfer->getBranch()->getIdBranch(),
            $billingPeriodTransfer->getBillingReference()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return array
     */
    protected function getInvoiceRefs(BillingPeriodTransfer $billingPeriodTransfer): array
    {
        $idsSalesOrder = [];
        foreach ($billingPeriodTransfer->getBillingItems() as $billingItem) {
            $idsSalesOrder[] = $billingItem->getFkSalesOrder();
        }

        return $this
            ->invoiceFacade
            ->getInvoiceReferencesForOrderIds($idsSalesOrder);
    }
}
