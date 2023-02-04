<?php
/**
 * Durst - project - BranchUserEmailNotUniqueException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:40
 */

namespace Pyz\Zed\Merchant\Business\Exception;

class BranchUserEmailNotUniqueException extends BranchUserException
{
    public const MESSAGE = 'Die Adresse "%s" ist bereits vergeben.';
}
