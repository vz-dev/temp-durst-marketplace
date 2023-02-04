<?php
/**
 * Durst - project - DiscountValidFromInPastException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 13:03
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountValidFromInPastException extends DiscountException
{
    public const MESSAGE = 'Der Startzeitpunkt liegt in der Vergangenheit.';
}
