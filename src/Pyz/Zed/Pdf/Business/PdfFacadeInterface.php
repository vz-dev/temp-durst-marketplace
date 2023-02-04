<?php
/**
 * Durst - project - PdfFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:17
 */

namespace Pyz\Zed\Pdf\Business;


use Generated\Shared\Transfer\PdfTransfer;

interface PdfFacadeInterface
{
    /**
     * Create a PDF file on disc and return the necessary information inside the response transfer
     *
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfFile(PdfTransfer $pdfTransfer): PdfTransfer;

    /**
     * Specification:
     *  - Returns the complete Path with file ending of a given file name
     *  - The path is prefixed as configured in
     * @see \Pyz\Shared\Pdf\PdfConstants::PDF_SAVE_PATH
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPdfNameWithPath(string $fileName): string;
}
