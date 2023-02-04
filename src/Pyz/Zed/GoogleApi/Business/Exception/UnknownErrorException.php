<?php
/**
 * Durst - project - UnknownErrorException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-10
 * Time: 20:40
 */

namespace Pyz\Zed\GoogleApi\Business\Exception;


class UnknownErrorException extends GoogleApiGeocodingException
{
    public const MESSAGE = 'An unknown Error occured during geocoding';
}
