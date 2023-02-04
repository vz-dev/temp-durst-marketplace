<?php
/**
 * Durst - project - GeocodingKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.19
 * Time: 12:33
 */

namespace Pyz\Zed\Graphhopper\Business\Handler\Json\Request;


interface GeocodingKeyRequestInterface
{
    public const GEOCODING_QUERY = 'q';
    public const GEOCODING_LOCALE = 'locale';
    public const GEOCODING_API_KEY = 'key';
    public const GEOCODING_RESULT_LIMIT = 'limit';
    public const GEOCODING_PROVIDER = 'provider';
}
