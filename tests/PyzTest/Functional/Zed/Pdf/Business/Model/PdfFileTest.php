<?php
namespace PyzTest\Functional\Zed\Pdf\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PdfTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit_Framework_MockObject_MockObject;
use Pyz\Zed\Pdf\Business\Exception\PdfHtmlNotSetException;
use Pyz\Zed\Pdf\Business\Exception\PdfTemplateNotSetException;
use Pyz\Zed\Pdf\Business\Model\PdfFile;
use Pyz\Zed\Pdf\Business\Model\PdfManager;
use Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridge;
use Pyz\Zed\Pdf\PdfConfig;
use Twig_Environment;
use Twig_Loader_Array;

class PdfFileTest extends Unit
{
    protected const INVALID_TEMPLATE_PATH = 'there/is/no/template/here';

    protected const PDF_HTML = '<h1>Dies ist ein Test</h1>';
    protected const PDF_FILE_NAME = 'Generated_Test.pdf';

    /**
     * @var \PyzTest\Functional\Zed\Pdf\PdfBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Pdf\Business\Model\PdfFileInterface
     */
    protected $pdfFile;

    /**
     * @var string
     */
    protected $generatedPdf;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $twig = $this
            ->createTwigEnvironment();

        $this->pdfFile = new PdfFile(
            new PdfManager(),
            new PdfToRendererBridge(
                $twig
            ),
            new PdfConfig()
        );
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
        @unlink($this->generatedPdf);
    }

    /**
     * @return void
     */
    public function testNoTemplateThrowsPdfTemplateNotSetException(): void
    {
        $this
            ->expectException(PdfTemplateNotSetException::class);

        $transfer = new PdfTransfer();

        $this
            ->pdfFile
            ->createPdfFile($transfer);
    }

    /**
     * @return void
     */
    public function testInvalidTemplateThrowsPdfHtmlNotSetException(): void
    {
        $this
            ->expectException(PdfHtmlNotSetException::class);

        $transfer = new PdfTransfer();
        $transfer
            ->setTemplate(static::INVALID_TEMPLATE_PATH);

        $this
            ->pdfFile
            ->createPdfFile($transfer);
    }

    /**
     * @return void
     */
    public function testPdfIsSuccessfullyWrittenToDisk(): void
    {
       $transfer = new PdfTransfer();
       $transfer
           ->setHtml(static::PDF_HTML)
           ->setFileName(static::PDF_FILE_NAME);

       $pdfTransfer = $this
           ->pdfFile
           ->createPdfFile($transfer);

       $this->generatedPdf = $pdfTransfer
           ->getFileName();

       $this
           ->assertTrue(
               file_exists(
                   $this
                       ->generatedPdf
               )
           );
    }

    /**
     * @return MockObject|Twig_Environment
     */
    protected function createTwigEnvironment(): PHPUnit_Framework_MockObject_MockObject
    {
        return $this
            ->getMockBuilder(Twig_Environment::class)
            ->setConstructorArgs(
                [
                    new Twig_Loader_Array()
                ]
            )
            ->setMethods(
                [
                    'render'
                ]
            )
            ->getMock();
    }
}
