<?php
/**
 * Durst - project - DepositSkuUnexpectedBranchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:45
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class DepositSkuUnexpectedBranchException extends Exception
{
    public const MESSAGE = 'The branch ID %d deviates from the expected branch ID %d for the deposit SKUs to update';
}
