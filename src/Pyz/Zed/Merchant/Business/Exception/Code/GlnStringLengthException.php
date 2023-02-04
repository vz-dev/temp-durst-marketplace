<?php
/**
 * Durst - project - GlnStringLengthException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:41
 */

namespace Pyz\Zed\Merchant\Business\Exception\Code;

class GlnStringLengthException extends GlnInvalidException
{
    public const MESSAGE = 'The gln must match the correct length of %d characters';
}
