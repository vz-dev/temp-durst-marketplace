<?php
/**
 * Durst - project - DiscountProductNotAvailableException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 12:21
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountProductNotAvailableException extends DiscountException
{
    public const MESSAGE = 'Das Produkt  "%s" - "%s" ist bereits in einem Angebot rabattiert.';
}
