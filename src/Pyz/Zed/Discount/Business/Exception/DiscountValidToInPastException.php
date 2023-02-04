<?php
/**
 * Durst - project - DiscountValidToInPastException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 13:05
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountValidToInPastException extends DiscountException
{
    public const MESSAGE = 'Der Endzeitpunkt liegt in der Vergangenheit.';
}
