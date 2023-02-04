<?php
/**
 * Durst - project - InvalidPaymentMethodException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 14:59
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class InvalidPaymentMethodException extends RuntimeException
{
    public const MESSAGE = 'Payment method %s is not supported';
}
