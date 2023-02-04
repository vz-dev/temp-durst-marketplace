<?php
/**
 * Durst - project - OverDailyLimitException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-10
 * Time: 20:30
 */

namespace Pyz\Zed\GoogleApi\Business\Exception;


class OverDailyLimitException extends GoogleApiGeocodingException
{
    public const MESSAGE = 'Over Daily Limit';
}
