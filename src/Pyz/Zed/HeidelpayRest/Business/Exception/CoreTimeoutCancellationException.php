<?php
/**
 * Durst - project - CoreTimeoutCancellationException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 17.08.20
 * Time: 14:17
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class CoreTimeoutCancellationException extends RuntimeException
{
    public const ERROR_CODE = 'API.340.000.009';
}
