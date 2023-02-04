<?php
/**
 * Durst - project - InvalidBase64StringException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 08:55
 */

namespace Pyz\Zed\Sales\Business\Exception;

use RuntimeException;

class InvalidBase64StringException extends RuntimeException
{
    public const MESSAGE = 'The passed string is not valid base 64 coded data';
}
