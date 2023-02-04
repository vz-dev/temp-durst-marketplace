<?php
/**
 * Durst - project - PasswordNotHashableException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-21
 * Time: 10:52
 */

namespace Pyz\Zed\Driver\Business\Exception;

use RuntimeException;

class PasswordNotHashableException extends RuntimeException
{
    public const MESSAGE = 'The password could not be hashed';
}
