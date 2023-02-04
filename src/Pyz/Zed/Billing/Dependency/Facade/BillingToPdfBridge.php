<?php
/**
 * Durst - project - BillingToPdfBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:52
 */

namespace Pyz\Zed\Billing\Dependency\Facade;

use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;

class BillingToPdfBridge implements BillingToPdfBridgeInterface
{
    /**
     * @var \Pyz\Zed\Pdf\Business\PdfFacadeInterface
     */
    protected $pdfFacade;

    /**
     * BillingToPdfBridge constructor.
     *
     * @param \Pyz\Zed\Pdf\Business\PdfFacadeInterface $pdfFacade
     */
    public function __construct(PdfFacadeInterface $pdfFacade)
    {
        $this->pdfFacade = $pdfFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PdfTransfer $pdfTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    public function createPdfFile(PdfTransfer $pdfTransfer): PdfTransfer
    {
        return $this
            ->pdfFacade
            ->createPdfFile($pdfTransfer);
    }
}
