<?php
/**
 * Durst - project - DownloadManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:32
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;

class DownloadManager implements DownloadManagerInterface
{
    /**
     * @var \Pyz\Zed\Billing\Business\Model\File\PdfManagerInterface
     */
    protected $pdfManager;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\File\ZipArchiveManagerInterface
     */
    protected $zipArchiveManager;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\File\CsvManagerInterface
     */
    protected $csvManager;

    /**
     * DownloadManager constructor.
     *
     * @param \Pyz\Zed\Billing\Business\Model\File\PdfManagerInterface $pdfManager
     * @param \Pyz\Zed\Billing\Business\Model\File\ZipArchiveManagerInterface $zipArchiveManager
     * @param \Pyz\Zed\Billing\Business\Model\File\CsvManagerInterface $csvManager
     */
    public function __construct(
        PdfManagerInterface $pdfManager,
        ZipArchiveManagerInterface $zipArchiveManager,
        CsvManagerInterface $csvManager
    ) {
        $this->pdfManager = $pdfManager;
        $this->zipArchiveManager = $zipArchiveManager;
        $this->csvManager = $csvManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    public function prepareDownload(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        $paths = $this
            ->pdfManager
            ->getAllInvoicePdfFilePathsForPeriod($billingPeriodTransfer);

        $paths[] = $this
            ->pdfManager
            ->mergePdfFiles($paths, $billingPeriodTransfer);

        $pdfTransfer = $this
            ->pdfManager
            ->createPdfForBillingPeriod($billingPeriodTransfer);

        $paths[] = $pdfTransfer->getFileName();
        $paths[] = $this->csvManager->createCsvForBillingPeriod($billingPeriodTransfer);

        return $this
            ->zipArchiveManager
            ->zipFilesAndGetPath($paths, $billingPeriodTransfer);
    }
}
