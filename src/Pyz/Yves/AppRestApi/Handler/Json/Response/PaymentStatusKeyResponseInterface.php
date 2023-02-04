<?php
/**
 * Durst - project - PaymentStatusKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.01.19
 * Time: 14:33
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface PaymentStatusKeyResponseInterface
{
    public const KEY_IS_SUCCESS = 'is_success';
    public const KEY_IS_PENDING = 'is_pending';
    public const KEY_IS_ERROR = 'is_error';
    public const KEY_ERROR_MESSAGE = 'error_message';
    public const KEY_REDIRECT_URL = 'redirect_url';
    public const KEY_RETURN_URL = 'return_url';
    public const KEY_PAYMENT_ID = 'payment_id';
}