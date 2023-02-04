<?php
/**
 * Durst - project - GraphmastersRequestInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 20:44
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface GraphmastersKeyRequestInterface
{
    public const KEY_MERCHANT_ID = 'merchant_id';
    public const KEY_ZIP_CODE = 'zip_code';

    public const KEY_CART = 'cart';
    public const KEY_CART_SKU = 'sku';
    public const KEY_CART_QUANTITY = 'quantity';
    public const KEY_WEIGHT = 'weight';

    public const KEY_SHIPPING_ADDRESS = 'shippingAddress';
    public const KEY_ADDRESS_SALUTATION = 'salutation';
    public const KEY_ADDRESS_FIRST_NAME = 'firstName';
    public const KEY_ADDRESS_LAST_NAME = 'lastName';
    public const KEY_ADDRESS_ADDRESS_1 = 'address1';
    public const KEY_ADDRESS_ADDRESS_2 = 'address2';
    public const KEY_ADDRESS_ADDRESS_3 = 'address3';
    public const KEY_ADDRESS_ZIP_CODE = 'zipCode';
    public const KEY_ADDRESS_CITY = 'city';
    public const KEY_ADDRESS_COMPANY = 'company';
    public const KEY_ADDRESS_PHONE = 'phone';
    public const KEY_ADDRESS_LAT = 'lat';
    public const KEY_ADDRESS_LNG = 'lng';
    public const KEY_ADDRESS_FLOOR = 'floor';
    public const KEY_ADDRESS_ELEVATOR = 'elevator';

    public const KEY_DEBUG = 'debug';
}
