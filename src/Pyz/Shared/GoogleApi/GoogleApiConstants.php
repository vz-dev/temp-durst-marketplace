<?php
/**
 * Durst - project - GoogleApiConstants.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 06:51
 */

namespace Pyz\Shared\GoogleApi;


class GoogleApiConstants
{
    public const GOOGLE_API_GEOCODING_KEY = 'GOOGLE_API_GEOCODING_KEY';

    public const GOOGLE_API_GEOCODING_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    public const GOOGLE_API_GEOCODING_COMPONENTS = 'country:DE';
    public const GOOGLE_API_GEOCODING_COMPONENTS_WITH_ZIP = 'postal_code:%s|country:DE';
}
