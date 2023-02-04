<?php
/**
 * Durst - project - CartDiscountGroupNotFound.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 14:35
 */

namespace Pyz\Zed\Discount\Business\Exception;


class CartDiscountGroupNotFound extends DiscountException
{
    public const MESSAGE = 'Es wurde keine Aktion mit der ID %d gefunden.';
}
