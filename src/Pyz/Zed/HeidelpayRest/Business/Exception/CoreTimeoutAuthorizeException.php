<?php
/**
 * Durst - project - CoreTimeoutException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.08.20
 * Time: 13:49
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class CoreTimeoutAuthorizeException extends RuntimeException
{
    public const ERROR_CODE = 'API.320.000.009';
    public const ERROR_CODE_ALTERNATIVE = 'API.320.001.999';
}
