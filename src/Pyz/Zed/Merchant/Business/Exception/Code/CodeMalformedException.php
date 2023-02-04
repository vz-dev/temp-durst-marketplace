<?php
/**
 * Durst - project - CodeMalformedException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:33
 */

namespace Pyz\Zed\Merchant\Business\Exception\Code;

class CodeMalformedException extends CodeNotValidException
{
    public const MESSAGE = 'The code must be a string containing exactly %d digits';
}
