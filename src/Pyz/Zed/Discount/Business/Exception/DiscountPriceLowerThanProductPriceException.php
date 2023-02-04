<?php
/**
 * Durst - project - DiscountPriceLowerThanProductPriceException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 12:20
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountPriceLowerThanProductPriceException extends DiscountException
{
    public const MESSAGE = 'Der Angebotspreis von %s für "%s" ist höher als der Produktpreis von %s.';
}
