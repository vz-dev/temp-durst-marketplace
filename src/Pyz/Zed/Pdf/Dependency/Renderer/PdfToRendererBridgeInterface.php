<?php
/**
 * Durst - project - PdfToRendererBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 10:29
 */

namespace Pyz\Zed\Pdf\Dependency\Renderer;


use Generated\Shared\Transfer\LocaleTransfer;

interface PdfToRendererBridgeInterface
{
    /**
     * @param string $template
     * @param array $options
     * @return string
     */
    public function render(
        string $template,
        array $options
    ): string;

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @return void
     */
    public function setLocaleTransfer(
        LocaleTransfer $localeTransfer
    ): void;
}
