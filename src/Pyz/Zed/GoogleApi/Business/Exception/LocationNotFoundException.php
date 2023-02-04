<?php
/**
 * Durst - project - LocationNotFoundException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-10
 * Time: 15:43
 */

namespace Pyz\Zed\GoogleApi\Business\Exception;


class LocationNotFoundException extends GoogleApiGeocodingException
{
    public const MESSAGE = 'No Location could be found for "%s".';
}
