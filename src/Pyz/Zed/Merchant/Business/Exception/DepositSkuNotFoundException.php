<?php
/**
 * Durst - project - DepositSkuNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:44
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class DepositSkuNotFoundException extends Exception
{
    public const DEPOSIT_SKU_NOT_FOUND_BRANCH_DEPOSIT_ID = 'No deposit skus found for the branch id %s and the deposit id %d';
}
