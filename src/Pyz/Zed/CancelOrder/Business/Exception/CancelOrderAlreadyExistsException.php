<?php
/**
 * Durst - project - CancelOrderAlreadyExistsException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 16:05
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

class CancelOrderAlreadyExistsException extends CancelOrderException
{
    public const MESSAGE = 'Diese Bestellung wurde bereits storniert.';
}
