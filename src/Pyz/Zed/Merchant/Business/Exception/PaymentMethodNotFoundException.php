<?php
/**
 * Durst - project - PaymentMethodNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:15
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class PaymentMethodNotFoundException extends Exception
{
    public const NOT_FOUND = 'A payment method with the id %d could not be found';
    public const NO_ID = 'You are trying to update a payment method without an id';
    public const CODE_NOT_FOUND = 'Payment method with code %s could not be found';
}
