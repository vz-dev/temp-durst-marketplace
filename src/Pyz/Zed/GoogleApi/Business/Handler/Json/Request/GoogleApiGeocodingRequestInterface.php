<?php
/**
 * Durst - project - GoogleApiGeocodingRequestInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 17:02
 */

namespace Pyz\Zed\GoogleApi\Business\Handler\Json\Request;


interface GoogleApiGeocodingRequestInterface
{
    public const GEOCODING_API_KEY = 'key';
    public const GEOCODING_ADDRESS_KEY = 'address';
    public const GEOCODING_COMPONENTS = 'components';
}
