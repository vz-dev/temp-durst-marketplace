<?php
/**
 * Durst - project - InvalidBranchException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.09.18
 * Time: 16:38
 */

namespace Pyz\Zed\Absence\Business\Exception;


class InvalidBranchException extends \Exception
{
    public const MESSAGE = 'Cannot delete absence for branch that is not logged in';
}