<?php
/**
 * Durst - project - DriverCancelOrderRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:08
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

interface DriverCancelOrderRequestInterface
{
    public const KEY_TOKEN = 'token';
    public const KEY_ORDER_ID = 'order_id';
    public const KEY_CANCEL_MESSAGE = 'cancel_message';
}
