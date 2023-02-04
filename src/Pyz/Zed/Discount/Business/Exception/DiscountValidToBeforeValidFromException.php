<?php
/**
 * Durst - project - DiscountValidToBeforeValidFromException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 13:06
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountValidToBeforeValidFromException extends DiscountException
{
    public const MESSAGE = 'Der Endzeitpunkt liegt vor dem Start der Aktion.';
}
