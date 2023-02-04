<?php
/**
 * Durst - project - PaymentMethodExistsException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:58
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class PaymentMethodExistsException extends Exception
{
    public const EXISTS_ID = 'There is already a payment method with the given id %d';
}
