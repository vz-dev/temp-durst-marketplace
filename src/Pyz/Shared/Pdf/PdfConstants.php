<?php
/**
 * Durst - project - PdfConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.02.20
 * Time: 15:28
 */

namespace Pyz\Shared\Pdf;


interface PdfConstants
{
    public const PDF_OPTION_MODE = 'mode';
    public const PDF_OPTION_FORMAT = 'format';
    public const PDF_OPTION_DEFAULT_FONT_SIZE = 'default_font_size';
    public const PDF_OPTION_DEFAULT_FONT = 'default_font';
    public const PDF_OPTION_MARGIN_LEFT = 'margin_left';
    public const PDF_OPTION_MARGIN_RIGHT = 'margin_right';
    public const PDF_OPTION_MARGIN_TOP = 'margin_top';
    public const PDF_OPTION_MARGIN_BOTTOM = 'margin_bottom';
    public const PDF_OPTION_MARGIN_HEADER = 'margin_header';
    public const PDF_OPTION_MARGIN_FOOTER = 'margin_footer';
    public const PDF_OPTION_ORIENTATION = 'orientation';

    public const PDF_SAVE_PATH = 'PDF_SAVE_PATH';
    public const PDF_ASSETS_PATH = 'PDF_ASSETS_PATH';

    public const PDF_MAIL_TO_PDF_TEMPLATE = 'PDF_MAIL_TO_PDF_TEMPLATE';
}
