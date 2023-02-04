<?php
/**
 * Durst - project - PriceImportCouldNotOpenCsvException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 16:21
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportCouldNotOpenCsvException extends PriceImportException
{
    public const MESSAGE = 'Konnte die Datei %s nicht öffnen.';
}
