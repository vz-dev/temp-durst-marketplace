<?php
/**
 * Durst - project - PdfManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.03.20
 * Time: 09:43
 */

namespace Pyz\Zed\Pdf\Business\Model;


use Generated\Shared\Transfer\PdfTransfer;

interface PdfManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdf(PdfTransfer $pdfTransfer): PdfTransfer;
}
