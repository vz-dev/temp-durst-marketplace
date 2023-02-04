<?php
/**
 * Durst - project - GoogleApiGeocodingResponseInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 17:03
 */

namespace Pyz\Zed\GoogleApi\Business\Handler\Json\Response;


interface GoogleApiGeocodingResponseInterface
{
    public const RESPONSE_RESULTS = 'results';
    public const RESPONSE_ADDRESS_COMPONENTS = 'address_components';
    public const RESPONSE_FORMATTED_ADDRESS = 'formatted_address';

    public const RESPONSE_GEOMETRY = 'geometry';
    public const RESPONSE_GEOMETRY_LOCATION = 'location';
    public const RESPONSE_GEOMETRY_LOCATION_LAT = 'lat';
    public const RESPONSE_GEOMETRY_LOCATION_LNG = 'lng';

    public const RESPONSE_STATUS = 'status';
    public const RESPONSE_STATUS_OK = 'OK';
    public const RESPONSE_STATUS_ZERO_RESULTS = 'ZERO_RESULTS';
    public const RESPONSE_STATUS_OVER_DAILY_LIMIT = 'OVER_DAILY_LIMIT';
    public const RESPONSE_STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
    public const RESPONSE_STATUS_REQUEST_DENIED = 'REQUEST_DENIED';
    public const RESPONSE_STATUS_INVALID_REQUEST = 'INVALID_REQUEST';
    public const RESPONSE_STATUS_UNKNOWN_ERROR = 'UNKNOWN_ERROR';
}
