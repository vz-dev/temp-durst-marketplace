<?php
/**
 * Durst - project - CodeExistsException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:32
 */

namespace Pyz\Zed\Merchant\Business\Exception\Code;

class CodeExistsException extends CodeNotValidException
{
    public const MESSAGE = 'There is already a branch with the code %s';
}
