<?php
/**
 * Durst - project - SalutationIdNotSetException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:28
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class SalutationIdNotSetException extends Exception
{
    public const NO_ID_SET = 'The transfer object has no id set. Use add(SalutationTransfer) instead to add a new salutation';
}
