<?php
/**
 * Durst - project - AmbiguousLocationException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 16:37
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class AmbiguousLocationException extends GeocodingException
{
    public const MESSAGE = 'Es wurden %d Adressen mit "%s" gefunden.';
}
