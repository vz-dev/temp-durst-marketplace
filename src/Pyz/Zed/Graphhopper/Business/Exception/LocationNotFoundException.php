<?php
/**
 * Durst - project - LocationNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 16:31
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class LocationNotFoundException extends GeocodingException
{
    public const MESSAGE = 'Es konnten keine Koordinaten für "%s" ermittelt werden.';
}
