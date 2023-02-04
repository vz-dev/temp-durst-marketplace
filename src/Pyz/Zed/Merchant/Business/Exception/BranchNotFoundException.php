<?php
/**
 * Durst - project - BranchNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:38
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class BranchNotFoundException extends Exception
{
    public const CODE_NOT_FOUND = 'No branch found with code %s';
    public const HASH_NOT_FOUND = 'No branch found with hash %s';
}
