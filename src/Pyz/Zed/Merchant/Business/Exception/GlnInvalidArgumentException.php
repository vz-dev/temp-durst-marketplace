<?php
/**
 * Durst - project - GlnInvalidArgumentException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:52
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use RuntimeException;

class GlnInvalidArgumentException extends RuntimeException
{
    public const MESSAGE = 'The format of the GLN "%s" is not valid';
}
