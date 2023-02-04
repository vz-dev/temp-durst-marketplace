<?php
/**
 * Durst - project - CancelOrderTokenNotSetException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.09.21
 * Time: 10:25
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

class CancelOrderTokenNotSetException extends CancelOrderException
{
    public const MESSAGE = 'Leider wurde kein Token gefunden.';
}
