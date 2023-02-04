<?php
/**
 * Durst - project - BranchInactiveException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:37
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class BranchInactiveException extends Exception
{
    public const MESSAGE = 'The branch with the code %s is inactive at the moment';
    public const MESSAGE_ID = 'The branch with the id %d is inactive at the moment';
}
