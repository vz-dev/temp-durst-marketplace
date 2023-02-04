<?php
/**
 * Durst - project - PdfFileInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:19
 */

namespace Pyz\Zed\Pdf\Business\Model;

use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Zed\Pdf\Business\Exception\PdfException;

interface PdfFileInterface
{
    /**
     * @param PdfTransfer $pdfTransfer
     *
     * @return PdfTransfer
     *
     * @throws PdfException
     */
    public function createPdfFile(
        PdfTransfer $pdfTransfer
    ): PdfTransfer;

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function getPdfNameWithPath(string $fileName): string;
}
