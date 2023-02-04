<?php
/**
 * Durst - project - RefundQuantityGreaterThanOrderQuantityException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 22:25
 */

namespace Pyz\Zed\Oms\Business\Exception;

use Exception;

class RefundQuantityGreaterThanOrderQuantityException extends Exception
{
    public const MESSAGE = "The refund quantity exceeds the orig. order item quantity.";
}
