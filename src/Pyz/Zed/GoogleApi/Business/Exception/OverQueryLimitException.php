<?php
/**
 * Durst - project - OverQueryLimitExceptionException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-10
 * Time: 20:32
 */

namespace Pyz\Zed\GoogleApi\Business\Exception;


class OverQueryLimitException extends GoogleApiGeocodingException
{
    public const MESSAGE = 'Over Query Limit';
}
