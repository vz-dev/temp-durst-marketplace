<?php
/**
 * Durst - project - PaymentTypeCannotAuthorizeExceptin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.01.19
 * Time: 10:26
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class PaymentTypeCannotAuthorizeException extends RuntimeException
{
    public const MESSAGE = 'The payment type does not use the trait CanAuthorize';
}
