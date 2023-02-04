<?php
/**
 * Durst - project - PriceImportInvalidIdException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.10.20
 * Time: 16:53
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportInvalidIdException extends PriceImportException
{
    public const MESSAGE = 'Der Preis Import mit der ID %d wurde nicht gefunden.';
}
