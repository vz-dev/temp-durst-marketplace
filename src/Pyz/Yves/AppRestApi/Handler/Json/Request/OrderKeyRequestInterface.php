<?php
/**
 * Durst - project - OrderKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:03
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

interface OrderKeyRequestInterface
{
    public const KEY_ITEMS = 'items';
    public const KEY_ITEM_SKU = 'sku';
    public const KEY_ITEM_QUANTITY = 'quantity';

    public const KEY_ID_BRANCH = 'idBranch';

    public const KEY_BILLING_ADDRESS = 'billingAddress';
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
    public const KEY_ADDRESS_COMMENT = 'comment';

    public const KEY_CUSTOMER = 'customer';
    public const KEY_CUSTOMER_SALUTATION = 'salutation';
    public const KEY_CUSTOMER_FIRST_NAME = 'firstName';
    public const KEY_CUSTOMER_LAST_NAME = 'lastName';
    public const KEY_CUSTOMER_EMAIL = 'email';
    public const KEY_CUSTOMER_COMPANY = 'company';
    public const KEY_CUSTOMER_PHONE = 'phone';
    public const KEY_CUSTOMER_ID = 'id';
    public const KEY_CUSTOMER_PRIVATE = 'isPrivate';

    public const KEY_MESSAGE = 'message';

    public const KEY_PAYMENT = 'payment';
    public const KEY_PAYMENT_DATE_OF_BIRTH = 'dateOfBirth';
    public const KEY_PAYMENT_SELECTION = 'selection';
    public const KEY_PAYMENT_METHOD = 'method';
    public const KEY_PAYMENT_PAYMENT_TYPE_ID = 'payment_type_id';
    public const KEY_PAYMENT_RETURN_URL = 'return_url';

    public const KEY_SHIPMENT = 'shipment';
    public const KEY_ID_CONCRETE_TIME_SLOT = 'idConcreteTimeSlot';

    public const KEY_PLATFORM_CLIENT = 'clientPlatform';
    public const KEY_DEVICE_TYPE = 'deviceType';
    public const KEY_CLIENT_VERSION = 'clientVersion';

    public const KEY_ID_TIME_SLOT_START = 'time_slot_start';
    public const KEY_ID_TIME_SLOT_END = 'time_slot_end';
}
