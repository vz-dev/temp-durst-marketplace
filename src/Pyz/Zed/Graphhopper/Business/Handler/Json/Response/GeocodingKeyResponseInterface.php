<?php
/**
 * Durst - project - GeocodingKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.19
 * Time: 12:36
 */

namespace Pyz\Zed\Graphhopper\Business\Handler\Json\Response;


interface GeocodingKeyResponseInterface
{
    public const RESPONSE_HITS = 'hits';
    public const RESPONSE_POINT = 'point';
    public const RESPONSE_LAT = 'lat';
    public const RESPONSE_LNG = 'lng';
}
