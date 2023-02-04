<?php
namespace PyzTest\Functional\Zed\Pdf\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PdfTransfer;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Pyz\Zed\Pdf\Business\Model\PdfManager;

class PdfManagerTest extends Unit
{
    protected const PDF_HTML = '<h1>Dies ist ein Test</h1>';

    protected const PDF_HEADER = '%PDF-1.4';

    /**
     * @var \PyzTest\Functional\Zed\Pdf\PdfBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Pdf\Business\Model\PdfManagerInterface
     */
    protected $pdfManager;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->pdfManager = new PdfManager();
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
    }

    /**
     * @return void
     */
    public function testPdfIsSuccessfullyCreated(): void
    {
        $pdfTransfer = $this
            ->createValidPdfTransfer();

        $pdfTransfer = $this
            ->pdfManager
            ->createPdf($pdfTransfer);

        $this
            ->assertStringStartsWith(
                static::PDF_HEADER,
                $pdfTransfer->getContent()
            );
    }

    /**
     * @return void
     */
    public function testNoHtmlOrTemplateThrowsMpdfException()
    {
        $this
            ->expectException(MpdfException::class);

        $pdfTransfer = $this
            ->createInvalidPdfTransfer();

        $this
            ->pdfManager
            ->createPdf($pdfTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function createValidPdfTransfer(): PdfTransfer
    {
        $transfer = new PdfTransfer();
        $transfer
            ->setPdfDestination(Destination::STRING_RETURN)
            ->setHtml(static::PDF_HTML);

        return $transfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function createInvalidPdfTransfer(): PdfTransfer
    {
        $transfer = new PdfTransfer();
        $transfer
            ->setPdfDestination(Destination::STRING_RETURN);

        return $transfer;
    }
}
