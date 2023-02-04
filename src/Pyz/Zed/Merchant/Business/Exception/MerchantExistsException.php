<?php
/**
 * Durst - project - MerchantExistsException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:53
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class MerchantExistsException extends Exception
{
    public const MESSAGE = 'Merchant with name %s already exists';
    public const MESSAGE_PIN = 'Merchant with pin %s already exists';
}
