<?php
/**
 * Durst - project - MerchantUserNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:56
 */

namespace Pyz\Zed\Merchant\Business\Exception;

class MerchantUserNotFoundException extends MerchantUserException
{
    public const MESSAGE = 'Der User mit der ID %d wurde nicht gefunden.';
    public const MESSAGE_EMAIL = 'Der User mit der Email %s wurde nicht gefunden.';
    public const MESSAGE_NOT_IN_SESSION = 'Es wurde kein User in der Session gefunden.';
}
