<?php
/**
 * Durst - project - CancelOrderTokenExpiredException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 13:35
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderTokenExpiredException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderTokenExpiredException extends CancelOrderException
{
    public const MESSAGE = 'Eine Stornierung ist leider nicht möglich, Auslieferung hat bereits begonnen.';
}
