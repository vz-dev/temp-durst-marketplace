<?php
/**
 * Durst - project - MerchantUserEmailNotUniqueException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:55
 */

namespace Pyz\Zed\Merchant\Business\Exception;

class MerchantUserEmailNotUniqueException extends MerchantUserException
{
    public const MESSAGE = 'Die Adresse "%s" ist bereits vergeben.';
}
