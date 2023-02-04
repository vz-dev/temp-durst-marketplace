<?php
/**
 * Durst - project - BranchStatusInvalidException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:40
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class BranchStatusInvalidException extends Exception
{
    public const MESSAGE = 'Branch status must be null or one of the following: "active", "blocked" or "deleted"';
}
