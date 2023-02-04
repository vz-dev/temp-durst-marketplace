<?php
/**
 * Durst - project - MerchantTimeSlotKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-24
 * Time: 12:37
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface MerchantTimeSlotKeyRequestInterface
{
    public const KEY_MERCHANT_ID = 'merchant_id';
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_USE_DAY_LIMIT = 'use_day_limit';

    public const KEY_CART = 'cart';
    public const KEY_CART_SKU = 'sku';
    public const KEY_CART_QUANTITY = 'quantity';
}
