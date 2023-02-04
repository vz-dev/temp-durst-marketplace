<?php
/**
 * Durst - project - DiscountKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 16:02
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DiscountKeyResponseInterface
{
    public const KEY_VALID = 'valid';
    public const KEY_ERROR_MESSAGE = 'error_message';
}
