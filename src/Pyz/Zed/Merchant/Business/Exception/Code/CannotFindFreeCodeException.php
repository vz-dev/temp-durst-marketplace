<?php
/**
 * Durst - project - CannotFindFreeCodeException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:30
 */

namespace Pyz\Zed\Merchant\Business\Exception\Code;

use Exception;

class CannotFindFreeCodeException extends Exception
{
    public const MESSAGE = 'Can\'t find a code with %d digits that isn\'t already taken. Maybe it\'s time to increase the amount of digits in the codes';
}
