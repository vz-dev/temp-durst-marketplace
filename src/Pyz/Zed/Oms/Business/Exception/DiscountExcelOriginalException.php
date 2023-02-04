<?php
/**
 * Durst - project - DiscountExcelOriginalException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.21
 * Time: 11:06
 */

namespace Pyz\Zed\Oms\Business\Exception;


class DiscountExcelOriginalException extends DiscountException
{
    public const MESSAGE = 'Der neue Wert des Discounts übersteigt den ursprünglichen Wert.';
}
