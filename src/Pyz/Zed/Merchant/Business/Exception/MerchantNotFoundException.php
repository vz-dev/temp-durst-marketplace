<?php
/**
 * Durst - project - MerchantNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:54
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class MerchantNotFoundException extends Exception
{
    public const MESSAGE_ID = 'Merchant with id %d could not be found';
    public const MESSAGE_ID_ACTIVE = 'No active merchant with id %d could not be found';
    public const MESSAGE_MERCHANTNAME = 'Merchant with merchantname %s could not be found';
    public const MESSAGE_NO_IN_SESSION = 'No merchant in current session';
    public const MESSAGE_PIN = 'Merchant with pin %s could not be found';
    public const MESSAGE_ID_BRANCH = 'No active merchant for branch %d';
    public const MESSAGE_ID_MERCHANT_USER = 'No active merchant for merchant user %d';
}
