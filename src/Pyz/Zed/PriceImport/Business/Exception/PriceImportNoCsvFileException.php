<?php
/**
 * Durst - project - PriceImportNoCsvFileException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 16:09
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportNoCsvFileException extends PriceImportException
{
    public const MESSAGE = 'Es wurde keine CSV Datei für den Import angegeben.';
}
