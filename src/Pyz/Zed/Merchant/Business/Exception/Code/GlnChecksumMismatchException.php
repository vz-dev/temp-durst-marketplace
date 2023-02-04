<?php
/**
 * Durst - project - GlnChecksumMismatchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:40
 */

namespace Pyz\Zed\Merchant\Business\Exception\Code;

class GlnChecksumMismatchException extends GlnInvalidException
{
    public const MESSAGE = 'The computed checksum of the gln is not valid.';
}
