<?php
/**
 * Durst - project - PriceImportInvalidHeaderException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.10.20
 * Time: 09:56
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportInvalidHeaderException extends PriceImportException
{
    public const MESSAGE = 'Die CSV Datei hat nicht die korrekte Kopfzeile.';
}
