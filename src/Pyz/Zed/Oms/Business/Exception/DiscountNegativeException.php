<?php
/**
 * Durst - project - DiscountNegativeException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.21
 * Time: 11:04
 */

namespace Pyz\Zed\Oms\Business\Exception;


class DiscountNegativeException extends DiscountException
{
    public const MESSAGE = 'Der Wert des Discount liegt im negativen Bereich.';
}
