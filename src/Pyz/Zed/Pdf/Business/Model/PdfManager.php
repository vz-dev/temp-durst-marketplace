<?php
/**
 * Durst - project - PdfManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.03.20
 * Time: 09:43
 */

namespace Pyz\Zed\Pdf\Business\Model;


use Generated\Shared\Transfer\PdfOptionsTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Pyz\Shared\Pdf\PdfConstants;

class PdfManager implements PdfManagerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     * @throws \Mpdf\MpdfException
     */
    public function createPdf(PdfTransfer $pdfTransfer): PdfTransfer
    {
        $mpdf = new Mpdf(
            $this
                ->getOptions($pdfTransfer)
        );

        $mpdf
            ->WriteHTML(
                $pdfTransfer
                    ->getHtml()
            );

        $content = $mpdf
            ->Output(
                $pdfTransfer
                    ->getFileName(),
                $pdfTransfer
                    ->getPdfDestination()
            );

        if (
            $pdfTransfer->getPdfDestination() === Destination::STRING_RETURN &&
            $content !== null
        ) {
            $pdfTransfer
                ->setContent($content);
        }

        return $pdfTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     * @return array
     */
    protected function getOptions(PdfTransfer $pdfTransfer): array
    {
        $optionsTransfer = $pdfTransfer
            ->getOptions();

        if ($optionsTransfer === null) {
            $optionsTransfer = $this
                ->getDefaultOptions();
        }

        return [
            PdfConstants::PDF_OPTION_MODE => $optionsTransfer->getMode(),
            PdfConstants::PDF_OPTION_FORMAT => $optionsTransfer->getFormat(),
            PdfConstants::PDF_OPTION_DEFAULT_FONT_SIZE => $optionsTransfer->getDefaultFontSize(),
            PdfConstants::PDF_OPTION_DEFAULT_FONT => $optionsTransfer->getDefaultFont(),
            PdfConstants::PDF_OPTION_MARGIN_LEFT => $optionsTransfer->getMarginLeft(),
            PdfConstants::PDF_OPTION_MARGIN_RIGHT => $optionsTransfer->getMarginRight(),
            PdfConstants::PDF_OPTION_MARGIN_TOP => $optionsTransfer->getMarginTop(),
            PdfConstants::PDF_OPTION_MARGIN_BOTTOM => $optionsTransfer->getMarginBottom(),
            PdfConstants::PDF_OPTION_MARGIN_HEADER => $optionsTransfer->getMarginHeader(),
            PdfConstants::PDF_OPTION_MARGIN_FOOTER => $optionsTransfer->getMarginFooter(),
            PdfConstants::PDF_OPTION_ORIENTATION => $optionsTransfer->getOrientation()
        ];
    }

    /**
     * @return \Generated\Shared\Transfer\PdfOptionsTransfer
     */
    protected function getDefaultOptions(): PdfOptionsTransfer
    {
        return (new PdfOptionsTransfer())
            ->setMode('')
            ->setFormat('A4')
            ->setDefaultFontSize(0)
            ->setDefaultFont('')
            ->setMarginLeft(15)
            ->setMarginRight(15)
            ->setMarginTop(16)
            ->setMarginBottom(16)
            ->setMarginHeader(9)
            ->setMarginFooter(9)
            ->setOrientation('P');
    }
}
