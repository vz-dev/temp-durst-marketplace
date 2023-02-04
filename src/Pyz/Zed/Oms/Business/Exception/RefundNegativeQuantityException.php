<?php
/**
 * Durst - project - RefundNegativeQuantityException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 22:26
 */

namespace Pyz\Zed\Oms\Business\Exception;

use Exception;

class RefundNegativeQuantityException extends Exception
{
    public const MESSAGE = "The refund quantity is negative.";
}
