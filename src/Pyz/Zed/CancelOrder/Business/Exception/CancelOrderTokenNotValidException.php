<?php
/**
 * Durst - project - CancelOrderTokenNotValidException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 13:45
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderTokenNotValidException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderTokenNotValidException extends CancelOrderException
{
    public const MESSAGE = 'Token ist leider nicht valide.';
}
