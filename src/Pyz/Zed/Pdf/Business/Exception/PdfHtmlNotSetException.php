<?php
/**
 * Durst - project - PdfHtmlNotSetException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 12:53
 */

namespace Pyz\Zed\Pdf\Business\Exception;


class PdfHtmlNotSetException extends PdfException
{
    public const MESSAGE = 'Es gibt kein HTML zur Ausgabe als PDF oder es konnte nicht generiert werden.';
}
