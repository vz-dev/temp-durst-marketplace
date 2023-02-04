<?php
/**
 * Durst - project - PriceImportInvalidMapperException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 17:02
 */

namespace Pyz\Zed\PriceImport\Business\Exception;


class PriceImportInvalidMapperException extends PriceImportException
{
    public const MESSAGE = 'Es ist kein Mapper mit dem Namen %s definiert.';
}
