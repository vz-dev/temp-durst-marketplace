<?php
/**
 * Durst - project - NoPaymentForOrder.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 15:05
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class PaymentNotFoundException extends RuntimeException
{
    public const MESSAGE = 'Payment for order with id %d could not be found';
    public const MESSAGE_ORDER_REF = 'Payment for order with order reference %s could not be found';
    public const MESSAGE_PAYMENT_ID = 'Payment with payment id %s could not be found';
}
