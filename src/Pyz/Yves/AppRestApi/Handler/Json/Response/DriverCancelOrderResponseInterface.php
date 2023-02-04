<?php
/**
 * Durst - project - DriverCancelOrderResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:08
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface DriverCancelOrderResponseInterface
{
    public const KEY_AUTH_VALID = 'auth_valid';
    public const KEY_ORDER_CANCELED = 'order_canceled';
    public const KEY_ALREADY_CANCELED = 'already_canceled';
    public const KEY_ERROR_MESSAGE = 'error_message';
}
