<?php
/**
 * Durst - project - PdfConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 14:53
 */

namespace Pyz\Zed\Pdf;


use Pyz\Shared\Pdf\PdfConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class PdfConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getPdfSavePath(): string
    {
        return $this
            ->get(PdfConstants::PDF_SAVE_PATH);
    }
}
