<?php
/**
 * Durst - project - PdfFile.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:19
 */

namespace Pyz\Zed\Pdf\Business\Model;

use Exception;
use Generated\Shared\Transfer\PdfTransfer;
use Mpdf\Output\Destination;
use Pyz\Zed\Pdf\Business\Exception\PdfException;
use Pyz\Zed\Pdf\Business\Exception\PdfHtmlNotSetException;
use Pyz\Zed\Pdf\Business\Exception\PdfSaveDirCouldNotBeCreatedException;
use Pyz\Zed\Pdf\Business\Exception\PdfTemplateNotSetException;
use Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridgeInterface;
use Pyz\Zed\Pdf\PdfConfig;

class PdfFile implements PdfFileInterface
{
    /**
     * @var \Pyz\Zed\Pdf\Business\Model\PdfManagerInterface
     */
    protected $pdfManager;

    /**
     * @var \Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridgeInterface
     */
    protected $pdfRenderer;

    /**
     * @var \Pyz\Zed\Pdf\PdfConfig
     */
    protected $config;

    /**
     * PdfFile constructor.
     *
     * @param \Pyz\Zed\Pdf\Business\Model\PdfManagerInterface $pdfManager
     * @param \Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridgeInterface $pdfRenderer
     * @param \Pyz\Zed\Pdf\PdfConfig $config
     */
    public function __construct(
        PdfManagerInterface $pdfManager,
        PdfToRendererBridgeInterface $pdfRenderer,
        PdfConfig $config
    ) {
        $this->pdfManager = $pdfManager;
        $this->pdfRenderer = $pdfRenderer;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param PdfTransfer $pdfTransfer
     * @return PdfTransfer
     * @throws PdfException
     */
    public function createPdfFile(
        PdfTransfer $pdfTransfer
    ): PdfTransfer {
        if ($pdfTransfer->getHtml() === null) {
            $pdfTransfer = $this
                ->renderHtml($pdfTransfer);
        }

        $pdfTransfer
            ->setPdfDestination(
                Destination::FILE
            );

        $this->createPdfSaveDirIfNecessary($pdfTransfer->getDirPath());

        $pdfTransfer
            ->setFileName(
                $this->getPdfNameWithPath($pdfTransfer->getFileName(), $pdfTransfer->getDirPath())
            );

        return $this
            ->generatePdf(
                $pdfTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $fileName
     * @return string
     */
    public function getPdfNameWithPath(string $fileName, ?string $path=null): string
    {
        if($path === null){
            $path = $this->config->getPdfSavePath();
        }

        return sprintf(
            '%s/%s',
            rtrim(
                $path,
                '/'
            ),
            $fileName
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     *
     * @throws \Pyz\Zed\Pdf\Business\Exception\PdfHtmlNotSetException
     * @throws \Pyz\Zed\Pdf\Business\Exception\PdfTemplateNotSetException
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function renderHtml(PdfTransfer $pdfTransfer): PdfTransfer
    {
        if ($pdfTransfer->getTemplate() === null) {
            throw new PdfTemplateNotSetException(
                PdfTemplateNotSetException::MESSAGE
            );
        }

        try {
            $html = $this
                ->pdfRenderer
                ->render(
                    $pdfTransfer->getTemplate(),
                    $pdfTransfer->getTemplateVariables()
                );
        } catch (Exception $exception) {
            throw new PdfHtmlNotSetException(
                $exception
            );
        }

        return $pdfTransfer
            ->setHtml($html);
    }

    /**
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function generatePdf(PdfTransfer $pdfTransfer): PdfTransfer
    {
        return $this
            ->pdfManager
            ->createPdf(
                $pdfTransfer
            );
    }

    /**
     * @param string|null $path
     * @throws PdfSaveDirCouldNotBeCreatedException
     */
    protected function createPdfSaveDirIfNecessary(?string $path=null)
    {
        if($path === null){
            $path = $this->config->getPdfSavePath();
        }

        if (is_dir($path) !== true) {
            $result = mkdir($path, 0755, true);

            if ($result !== true) {
                throw new PdfSaveDirCouldNotBeCreatedException(
                    sprintf(
                        PdfSaveDirCouldNotBeCreatedException::MESSAGE,
                        $path
                    )
                );
            }
        }
    }
}
