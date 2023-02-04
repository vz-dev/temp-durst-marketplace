<?php
/**
 * Durst - project - RequestDeniedException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-10
 * Time: 20:35
 */

namespace Pyz\Zed\GoogleApi\Business\Exception;


use Pyz\Zed\Graphhopper\Business\Exception\GeocodingException;

class RequestDeniedException extends GeocodingException
{
    public const MESSAGE = 'Your request was denied - address used : %s';
}
