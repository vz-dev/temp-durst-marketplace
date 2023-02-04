<?php
/**
 * Durst - project - BranchPriceModeInvalidException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:39
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class BranchPriceModeInvalidException extends Exception
{
    public const MESSAGE = 'Branch status must be null or one of the following: "NET_MODE" or "GROSS_MODE"';
}
