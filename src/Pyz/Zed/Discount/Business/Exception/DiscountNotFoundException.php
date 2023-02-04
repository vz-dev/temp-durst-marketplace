<?php
/**
 * Durst - project - DiscountNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.06.21
 * Time: 08:01
 */

namespace Pyz\Zed\Discount\Business\Exception;


class DiscountNotFoundException extends DiscountException
{
    public const MESSAGE = 'Es wurde kein Rabatt mit der ID %d gefunden.';
}
