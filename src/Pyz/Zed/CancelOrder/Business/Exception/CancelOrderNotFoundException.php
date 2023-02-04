<?php
/**
 * Durst - project - CancelOrderNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.09.21
 * Time: 11:19
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderNotFoundException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderNotFoundException extends CancelOrderException
{
    public const MESSAGE = 'Die Stornierung mit der ID "%d" wurde nicht gefunden.';
}
