<?php
/**
 * Durst - project - CancelOrderSalesOrderNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 09:36
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderSalesOrderNotFoundException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderSalesOrderNotFoundException extends CancelOrderException
{
    public const MESSAGE = 'Die Bestellung mit der ID %d wurde nicht gefunden.';
}
