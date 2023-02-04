<?php
/**
 * Durst - project - TimeSlotKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

interface TimeSlotKeyRequestInterface
{
    public const KEY_MERCHANT_IDS = 'merchant_ids';
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_MAX_SLOTS = 'max_slots';
    public const KEY_ITEMS_PER_SLOT = 'items_per_slot';

    public const KEY_CART = 'cart';
    public const KEY_CART_SKU = 'sku';
    public const KEY_CART_QUANTITY = 'quantity';
}
