<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverTourResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;
    public const KEY_TOURS = 'tours';
    public const KEY_TOUR_TOUR_ID = 'tour_id';
    public const KEY_TOUR_TOUR_REFERENCE = 'tour_reference';
    public const KEY_TOUR_IS_DELIVERABLE = 'is_deliverable';
    public const KEY_TOUR_DELIVERY_AREAS = 'delivery_areas';
    public const KEY_TOUR_TOUR_DATE = 'tour_date';
    public const KEY_TOUR_TOUR_START = 'tour_start';
    public const KEY_TOUR_TOUR_END = 'tour_end';
    public const KEY_TOUR_WAREHOUSE_LAT = 'warehouse_lat';
    public const KEY_TOUR_WAREHOUSE_LNG = 'warehouse_lng';
    public const KEY_TOUR_COMMENT = 'comment';
    public const KEY_TOUR_ORDERS = 'orders';
    public const KEY_TOUR_ORDERS_COMMENTS = 'comments';
    public const KEY_TOUR_ORDERS_COMMENTS_TYPE = 'type';
    public const KEY_TOUR_ORDERS_COMMENTS_MESSAGE = 'message';
    public const KEY_TOUR_ORDERS_ORDER_ID = 'order_id';
    public const KEY_TOUR_ORDERS_ORDER_REFERENCE = 'order_reference';
    public const KEY_TOUR_ORDERS_IS_EXTERNAL= 'is_external';
    public const KEY_TOUR_ORDERS_IS_PRIVATE= 'is_private';
    public const KEY_TOUR_ORDERS_DELIVERY_ORDER = 'delivery_order';
    public const KEY_TOUR_ORDERS_TIME_SLOT_FROM = 'time_slot_from';
    public const KEY_TOUR_ORDERS_TIME_SLOT_TO = 'time_slot_to';
    public const KEY_TOUR_ORDERS_PAYMENT_METHOD = 'payment_method';
    public const KEY_TOUR_ORDERS_PAYMENT_CODE = 'payment_code';
    public const KEY_TOUR_ORDERS_CUSTOMER_NOTE = 'customer_note';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM = 'gtin_to_order_item';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_GTIN = 'gtin';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS = 'order_items';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_ORDER_ITEM_ID = 'order_item_id';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_UNIT_NAME = 'unit_name';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_PRICE_SINGLE = 'price_single';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_PRICE_TOTAL = 'price_total';
    public const KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_DEPOSIT_SINGLE = 'deposit_single';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS = 'order_items';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_ORDER_ITEM_ID = 'order_item_id';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_GTIN = 'gtin';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_SKU = 'sku';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_QUANTITY = 'quantity';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_PRODUCT_NAME = 'product_name';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_UNIT_NAME = 'unit_name';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_TAX_RATE = 'tax_rate';
    public const KEY_TOUR_ORDERS_ORDER_ITEMS_TAX_AMOUNT = 'tax_amount';
    public const KEY_TOUR_ORDERS_CUSTOMER = 'customer';
    public const KEY_TOUR_MODE = 'travel_mode';
    public const KEY_TOUR_ORDERS_CUSTOMER_SALUTATION = 'salutation';
    public const KEY_TOUR_ORDERS_CUSTOMER_FIRST_NAME = 'first_name';
    public const KEY_TOUR_ORDERS_CUSTOMER_LAST_NAME = 'last_name';
    public const KEY_TOUR_ORDERS_CUSTOMER_EMAIL = 'email';
    public const KEY_TOUR_ORDERS_CUSTOMER_COMPANY = 'company';
    public const KEY_TOUR_ORDERS_CUSTOMER_PHONE = 'phone';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS = 'shipping_address';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_SALUTATION = 'salutation';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_FIRST_NAME = 'first_name';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_LAST_NAME = 'last_name';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_1 = 'address_1';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_2 = 'address_2';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_3 = 'address_3';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ZIP_CODE = 'zip_code';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_COMMENT = 'comment';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_FLOOR = 'floor';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ELEVATOR = 'elevator';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_CITY = 'city';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_COMPANY = 'company';
    public const KEY_TOUR_ORDERS_SHIPPING_ADDRESS_PHONE = 'phone';
    public const KEY_TOUR_ORDERS_DISCOUNTS = 'discounts';
    public const KEY_TOUR_ORDERS_DISCOUNTS_ID = 'id';
    public const KEY_TOUR_ORDERS_DISCOUNTS_NAME = 'name';
    public const KEY_TOUR_ORDERS_DISCOUNTS_AMOUNT = 'amount';
    public const KEY_TOUR_ORDERS_DISCOUNTS_EXPENSE_TYPE = 'expense_type';
    public const KEY_TOUR_ORDERS_DISCOUNTS_MIN_SUB_TOTAL = 'min_sub_total';
}
