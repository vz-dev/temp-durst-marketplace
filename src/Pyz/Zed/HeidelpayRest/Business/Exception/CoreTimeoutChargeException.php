<?php
/**
 * Durst - project - CoreTimeoutChargeException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 17.08.20
 * Time: 14:16
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class CoreTimeoutChargeException extends RuntimeException
{
    public const ERROR_CODE = 'API.330.000.009';
}
