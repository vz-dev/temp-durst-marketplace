<?php
/**
 * Durst - project - InvoicePdf.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 15:14
 */

namespace Pyz\Zed\Invoice\Business\Model;

use DateTime;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfOptionsTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\Invoice\Business\Exception\FileNotFoundException;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridgeInterface;
use Pyz\Zed\Invoice\InvoiceConfig;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

class InvoicePdf implements InvoicePdfInterface
{
    protected const SURVEY_DATE_FORMAT = 'dmy';

    protected const INVOICE_FILE_NAME = '%s_%d.pdf';
    protected const INVOICE_PDF_NAME = 'Rechnung %s.pdf';

    /**
     * @var \Pyz\Zed\Invoice\InvoiceConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Pdf\Business\PdfFacadeInterface
     */
    protected $pdfFacade;

    /**
     * @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridgeInterface
     */
    protected $heidelpayRestFacade;

    /**
     * @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridgeInterface
     */
    protected $omsFacade;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * InvoicePdf constructor.
     *
     * @param \Pyz\Zed\Invoice\InvoiceConfig $config
     * @param \Pyz\Zed\Pdf\Business\PdfFacadeInterface $pdfFacade
     * @param \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridgeInterface $heidelpayRestFacade
     * @param \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridgeInterface $omsFacade
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     */
    public function __construct(
        InvoiceConfig $config,
        PdfFacadeInterface $pdfFacade,
        InvoiceToHeidelpayRestBridgeInterface $heidelpayRestFacade,
        InvoiceToOmsBridgeInterface $omsFacade,
        Filesystem $fileSystem
    ) {
        $this->config = $config;
        $this->pdfFacade = $pdfFacade;
        $this->heidelpayRestFacade = $heidelpayRestFacade;
        $this->omsFacade = $omsFacade;
        $this->fileSystem = $fileSystem;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdf(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): PdfTransfer {
        $termsOfService = $branchTransfer
            ->getTermsOfService();
        $termsOfServiceLines = explode(
            PHP_EOL,
            $termsOfService
        );

        $branchTransfer
            ->setTermsOfService($termsOfService)
            ->setTermsOfServiceLines($termsOfServiceLines);

        $pdfTransfer = $this
            ->createPdfTransfer($this->createMailTransfer($orderTransfer, $branchTransfer));

        return $this
            ->pdfFacade
            ->createPdfFile($pdfTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createInvoicePdfFromMailTransfer(MailTransfer $mailTransfer): PdfTransfer
    {
        return $this
            ->pdfFacade
            ->createPdfFile(
                $this->createPdfTransfer($mailTransfer)
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    public function getInvoicePdfFilePathForOrder(string $invoiceReference, int $idBranch): string
    {
        $filePath = $this
            ->pdfFacade
            ->getPdfNameWithPath(
                $this->getPdfFileName(
                    $invoiceReference,
                    $idBranch
                )
            );

        if ($this->fileSystem->exists($filePath) !== true) {
            throw FileNotFoundException::build($filePath);
        }

        return $filePath;
    }

    /**
     * @param string $invoiceReference
     * @param int $idBranch
     *
     * @return string
     */
    protected function getPdfFileName(
        string $invoiceReference,
        int $idBranch
    ): string {
        return sprintf(
            static::INVOICE_FILE_NAME,
            $invoiceReference,
            $idBranch
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): MailTransfer {
        return (new MailTransfer())
            ->setBranch($branchTransfer)
            ->setOrder($orderTransfer)
            ->setBaseUrl($this->config->getBaseUrl())
            ->setDurst($this->omsFacade->createDurstCompanyTransfer())
            ->setSurveyUrls($this->buildSurveyUrls($orderTransfer, $branchTransfer))
            ->setShortId($this->getShortIdByIdSalesOrder($orderTransfer->getIdSalesOrder()));
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return array
     */
    protected function buildSurveyUrls(
        OrderTransfer $orderTransfer,
        BranchTransfer $branchTransfer
    ): array {
        $surveyUrls = [];

        $happinessTypes = $this
            ->config
            ->getSurveyHappinessTypes();

        foreach ($happinessTypes as $happinessType) {
            $surveyUrls[$happinessType] = sprintf(
                $this->config->getSurveyUrl(),
                $happinessType,
                $orderTransfer->getShippingAddress()->getZipCode(),
                $branchTransfer->getIdBranch(),
                $this->getDeliveryDateString($orderTransfer->getConcreteTimeSlot()->getStartTime()),
                $orderTransfer->getTotals()->getGrandTotal(),
                rawurlencode($orderTransfer->getOrderReference())
            );
        }

        return $surveyUrls;
    }

    /**
     * @param string $oderDate
     *
     * @return string
     */
    protected function getDeliveryDateString(string $oderDate): string
    {
        $date = DateTime::createFromFormat(
            DateTime::RFC3339,
            $oderDate
        );

        return $date
            ->format(
                static::SURVEY_DATE_FORMAT
            );
    }

    /**
     * @param int $idSalesOrder
     *
     * @return string|null
     */
    protected function getShortIdByIdSalesOrder(int $idSalesOrder): ?string
    {
        $logTransfer = $this
            ->heidelpayRestFacade
            ->getHeidelpayRestLogByIdSalesOrderAndTransactionType(
                $idSalesOrder,
                HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
            );

        if ($logTransfer === null) {
            return null;
        }

        return $logTransfer
            ->getShortId();
    }

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function createPdfTransfer(MailTransfer $mailTransfer): PdfTransfer
    {
        $pdfTransfer = new PdfTransfer();

        $pdfTransfer
            ->setTemplateVariables(
                [
                    'mail' => $mailTransfer,
                ]
            )
            ->setTemplate(
                $this
                    ->config
                    ->getPdfMailToPdfTemplate()
            )
            ->setFileName(
                $this->getPdfFileName(
                    $mailTransfer->getOrder()->getInvoiceReference(),
                    $mailTransfer->getBranch()->getIdBranch()
                )
            )
            ->setPdfName(
                sprintf(
                    static::INVOICE_PDF_NAME,
                    $mailTransfer->getOrder()->getInvoiceReference()
                )
            )
            ->setDisplayName(
                sprintf(
                    static::INVOICE_PDF_NAME,
                    $mailTransfer->getOrder()->getInvoiceReference()
                )
            )
            ->setOptions(
                $this
                    ->createPdfOptions()
            );

        return $pdfTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PdfOptionsTransfer
     */
    protected function createPdfOptions(): PdfOptionsTransfer
    {
        return (new PdfOptionsTransfer())
            ->setMode('')
            ->setFormat('A4')
            ->setDefaultFontSize(0)
            ->setDefaultFont('')
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(20)
            ->setMarginBottom(30)
            ->setMarginHeader(0)
            ->setMarginFooter(0)
            ->setOrientation('P');
    }
}
