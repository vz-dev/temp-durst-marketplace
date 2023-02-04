<?php
/**
 * Durst - project - OrderKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface OrderKeyResponseInterface
{
    public const KEY_ORDER_CONFIRMATION = 'order_confirmation';
    public const KEY_ORDER_REFERENCE = 'order_ref';
    public const KEY_SHIPPING_MERCHANT = 'merchant';

    public const KEY_CHECKOUT_ERRORS = 'checkout_errors';
    public const KEY_CHECKOUT_ERROR_CODE = 'code';
    public const KEY_CHECKOUT_ERROR_MESSAGE = 'message';

    public const KEY_PAYMENT_PENDING = 'is_pending';
    public const KEY_PAYMENT_REDIRECT_URL = 'redirect_url';
    public const KEY_PAYMENT_RETURN_URL = 'return_url';
    public const KEY_PAYMENT_PAYMENT_ID = 'payment_id';
}
