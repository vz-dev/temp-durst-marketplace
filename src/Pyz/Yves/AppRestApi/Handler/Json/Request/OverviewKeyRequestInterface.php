<?php
/**
 * Durst - project - OverviewKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 13:50
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface OverviewKeyRequestInterface
{
    public const KEY_TIME_SLOT_ID = 'time_slot_id';

    public const KEY_CART = 'cart';
    public const KEY_CART_SKU = 'sku';
    public const KEY_CART_QUANTITY = 'quantity';

    public const KEY_VOUCHER_CODE = 'voucher_code';

    public const KEY_SHIPPING_ADDRESS = 'shippingAddress';
    public const KEY_ADDRESS_ZIP_CODE = 'zipCode';

    // v2
    public const KEY_BRANCH_ID = 'branch_id';
}
