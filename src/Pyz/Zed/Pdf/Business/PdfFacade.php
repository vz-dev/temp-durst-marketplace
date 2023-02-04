<?php
/**
 * Durst - project - PdfFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:17
 */

namespace Pyz\Zed\Pdf\Business;

use Generated\Shared\Transfer\PdfTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class PdfFacade
 * @package Pyz\Zed\Pdf\Business
 * @method \Pyz\Zed\Pdf\Business\PdfBusinessFactory getFactory()
 */
class PdfFacade extends AbstractFacade implements PdfFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfFile(PdfTransfer $pdfTransfer): PdfTransfer
    {
        return $this
            ->getFactory()
            ->createPdfFileModel()
            ->createPdfFile(
                $pdfTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $fileName
     * @return string
     */
    public function getPdfNameWithPath(string $fileName): string
    {
        return $this
            ->getFactory()
            ->createPdfFileModel()
            ->getPdfNameWithPath($fileName);
    }
}
