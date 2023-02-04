<?php
/**
 * Durst - project - InvalidArgumentException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 14:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class InvalidArgumentException extends RuntimeException
{
    public const MESSAGE = 'Payment with id %d could not be found';
    public const PAYMENT_EXISTS = 'Payment object with id %d already exists and therefore cannot be created';
}
