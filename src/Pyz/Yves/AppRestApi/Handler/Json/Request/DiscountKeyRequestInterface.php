<?php
/**
 * Durst - project - DiscountKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 15:49
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface DiscountKeyRequestInterface
{
    public const KEY_BRANCH_ID = 'branch_id';
    public const KEY_TIME_SLOT_ID = 'time_slot_id';
    public const KEY_VOUCHER_CODE = 'voucher_code';

    public const KEY_CART = 'cart';
    public const KEY_CART_SKU = 'sku';
    public const KEY_CART_QUANTITY = 'quantity';

    public const KEY_SHIPPING_ADDRESS = 'shipping_address';
    public const KEY_SHIPPING_ADDRESS_SALUTATION = 'salutation';
    public const KEY_SHIPPING_ADDRESS_FIRST_NAME = 'firstName';
    public const KEY_SHIPPING_ADDRESS_LAST_NAME = 'lastName';
    public const KEY_SHIPPING_ADDRESS_ADDRESS_1 = 'address1';
    public const KEY_SHIPPING_ADDRESS_ADDRESS_2 = 'address2';
    public const KEY_SHIPPING_ADDRESS_ADDRESS_3 = 'address3';
    public const KEY_SHIPPING_ADDRESS_ZIP_CODE = 'zipCode';
    public const KEY_SHIPPING_ADDRESS_CITY = 'city';
    public const KEY_SHIPPING_ADDRESS_COMPANY = 'company';
    public const KEY_SHIPPING_ADDRESS_PHONE = 'phone';
    public const KEY_SHIPPING_ADDRESS_LAT = 'lat';
    public const KEY_SHIPPING_ADDRESS_LNG = 'lng';
    public const KEY_SHIPPING_ADDRESS_FLOOR = 'floor';
    public const KEY_SHIPPING_ADDRESS_ELEVATOR = 'elevator';
    public const KEY_SHIPPING_ADDRESS_COMMENT = 'comment';
}
