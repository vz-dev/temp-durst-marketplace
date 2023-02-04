<?php
/**
 * Durst - project - PdfTemplateNotSetException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:43
 */

namespace Pyz\Zed\Pdf\Business\Exception;


class PdfTemplateNotSetException extends PdfException
{
    public const MESSAGE = 'Das Template wurde nicht gefunden.';
}
