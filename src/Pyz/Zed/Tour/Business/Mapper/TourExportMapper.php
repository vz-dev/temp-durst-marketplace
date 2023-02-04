<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-04
 * Time: 16:16
 */

namespace Pyz\Zed\Tour\Business\Mapper;

use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;

class TourExportMapper
{
    public const PAYLOAD_CREATE_DATE = 'create_date';
    public const PAYLOAD_CREATE_TIME = 'create_time';
    public const PAYLOAD_ILN_SENDER = 'iln_sender';
    public const PAYLOAD_ILN_RECIPIENT = 'iln_recipient';
    public const PAYLOAD_ILN_DELIVERY = 'iln_delivery';
    public const PAYLOAD_TOUR_NUMBER = 'tour_number';
    public const PAYLOAD_DELIVERY_DATE = 'delivery_date';
    public const PAYLOAD_DELIVERY_TIME = 'delivery_time';
    public const PAYLOAD_IS_RETURN_ITEM = 'is_return_item';
    public const PAYLOAD_DRIVER = 'driver';
    public const PAYLOAD_BILLING_REFERENCE = 'billing_reference';
    public const PAYLOAD_ACCESS_TOKEN = 'access_token';
    public const PAYLOAD_DATA_TRANSFER_REFERENCE = 'reference_number';
    public const PAYLOAD_MESSAGE_REFERENCE = 'message_reference';
    public const PAYLOAD_CREATE_DATETIME = 'create_datetime';
    public const PAYLOAD_DELIVERY_DATETIME = 'delivery_datetime';
    public const PAYLOAD_GTIN = 'gtin';
    public const PAYLOAD_DURST_SKU = 'durst_sku';
    public const PAYLOAD_PRODUCT_DESCRIPTION = 'product_description';
    public const PAYLOAD_MERCHANT_SKU = 'merchant_sku';
    public const PAYLOAD_QUANTITY = 'quantity';
    public const PAYLOAD_DESCRIPTION_RETURN_ITEM = 'description_return_item';
    public const PAYLOAD_BASIC_AUTH_USERNAME = 'basic_auth_username';
    public const PAYLOAD_BASIC_AUTH_PASSWORD = 'basic_auth_password';
    public const PAYLOAD_ORDER_REFERENCE = 'order_reference';
    public const PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE = 'order_durst_customer_reference';
    public const PAYLOAD_ORDER_ID = 'order_id';
    public const PAYLOAD_ORDER_ITEMS = 'order_items';
    public const PAYLOAD_ORDER_ITEM_ORDER_REFERENCE  = 'order_item_order_reference';
    public const PAYLOAD_ORDER_ITEM_PRICE_TO_PAY = 'order_item_price_to_pay';
    public const PAYLOAD_ORDER_ITEM_FK_SALES_ORDER = 'order_item_fk_sales_order';

    public const EDI_CREATE_DATE = 'date';
    public const EDI_CREATE_TIME = 'time';
    public const EDI_ILN_SENDER = 'sender_gln';
    public const EDI_ILN_RECIPIENT = 'recipient_gln';
    public const EDI_ILN_DELIVERY = 'delivery_iln';
    public const EDI_TOUR_NUMBER = 'tour_no';
    public const EDI_DELIVERY_DATE = 'shipping_date';
    public const EDI_DELIVERY_TIME = 'shipping_time';
    public const EDI_IS_RETURN_ITEM = 'qualifier_return';
    public const EDI_DRIVER = 'driver';
    public const EDI_BILLING_REFERENCE = 'billing_reference';
    public const EDI_ACCESS_TOKEN = 'access_token';
    public const EDI_DATA_TRANSFER_REFERENCE = 'reference_number';
    public const EDI_MESSAGE_REFERENCE = 'message_reference';
    public const EDI_CREATE_DATETIME = 'create_datetime';
    public const EDI_DELIVERY_DATETIME = 'delivery_datetime';
    public const EDI_GTIN = 'gtin';
    public const EDI_DURST_SKU = 'durst_sku';
    public const EDI_PRODUCT_DESCRIPTION = 'product_description';
    public const EDI_MERCHANT_SKU = 'sku';
    public const EDI_QUANTITY = 'quantity';
    public const EDI_DESCRIPTION_RETURN_ITEM = 'reason_return';
    public const EDI_BASIC_AUTH_USERNAME = 'username_basic_auth';
    public const EDI_BASIC_AUTH_PASSWORD = 'password_basic_auth';
    public const EDI_ORDER_REFERENCE = 'order_reference';
    public const EDI_ORDER_DURST_CUSTOMER_REFERENCE = 'order_durst_customer_reference';
    public const EDI_ORDER_ID = 'order_id';
    public const EDI_ORDER_ITEMS = 'order_items';
    public const EDI_ORDER_ITEM_ORDER_REFERENCE  = 'order_item_order_reference';
    public const EDI_ORDER_ITEM_PRICE_TO_PAY = 'order_item_price_to_pay';
    public const EDI_ORDER_ITEM_FK_SALES_ORDER = 'order_item_fk_sales_order';

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactFacade;

    /**
     * @param EdifactFacadeInterface $edifactFacade
     */
    public function __construct(EdifactFacadeInterface $edifactFacade)
    {
        $this->edifactFacade = $edifactFacade;
    }

    /**
     * @param array $payload
     * @return array
     */
    public function map(array $payload): array
    {
        $exportVersion = $this->edifactFacade->getExportVersion();

        $result = [
            static::EDI_CREATE_DATE => $payload[static::PAYLOAD_CREATE_DATE],
            static::EDI_CREATE_TIME => $payload[static::PAYLOAD_CREATE_TIME],
            static::EDI_ILN_SENDER => $payload[static::PAYLOAD_ILN_SENDER],
            static::EDI_ILN_RECIPIENT => $payload[static::PAYLOAD_ILN_RECIPIENT],
            static::EDI_ILN_DELIVERY => $payload[static::PAYLOAD_ILN_DELIVERY],
            static::EDI_TOUR_NUMBER => $payload[static::PAYLOAD_TOUR_NUMBER],
            static::EDI_DELIVERY_DATE => $payload[static::PAYLOAD_DELIVERY_DATE],
            static::EDI_DELIVERY_TIME => $payload[static::PAYLOAD_DELIVERY_TIME],
            static::EDI_IS_RETURN_ITEM => $payload[static::PAYLOAD_IS_RETURN_ITEM],
            static::EDI_DRIVER => $payload[static::PAYLOAD_DRIVER],
            static::EDI_BILLING_REFERENCE => $payload[static::PAYLOAD_BILLING_REFERENCE],
            static::EDI_ACCESS_TOKEN => $payload[static::PAYLOAD_ACCESS_TOKEN],
            static::EDI_DATA_TRANSFER_REFERENCE => $payload[static::PAYLOAD_DATA_TRANSFER_REFERENCE],
            static::EDI_MESSAGE_REFERENCE => $payload[static::PAYLOAD_MESSAGE_REFERENCE],
            static::EDI_CREATE_DATETIME => $payload[static::PAYLOAD_CREATE_DATETIME],
            static::EDI_DELIVERY_DATETIME => $payload[static::PAYLOAD_DELIVERY_DATETIME],
            static::EDI_DESCRIPTION_RETURN_ITEM => $payload[static::PAYLOAD_DESCRIPTION_RETURN_ITEM],
            static::EDI_BASIC_AUTH_USERNAME => $payload[static::PAYLOAD_BASIC_AUTH_USERNAME],
            static::EDI_BASIC_AUTH_PASSWORD => $payload[static::PAYLOAD_BASIC_AUTH_PASSWORD],
        ];

        if ($exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
            $result = array_merge($result, [
                static::EDI_GTIN => $payload[static::PAYLOAD_GTIN],
                static::EDI_DURST_SKU => $payload[static::PAYLOAD_DURST_SKU],
                static::EDI_PRODUCT_DESCRIPTION => $payload[static::PAYLOAD_PRODUCT_DESCRIPTION],
                static::EDI_MERCHANT_SKU => $payload[static::PAYLOAD_MERCHANT_SKU],
                static::EDI_QUANTITY => $payload[static::PAYLOAD_QUANTITY],
            ]);
        } else if ($exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $result = array_merge($result, [
                static::EDI_ORDER_ID => $payload[static::PAYLOAD_ORDER_ID] ?? null,
                static::EDI_ORDER_REFERENCE => $payload[static::PAYLOAD_ORDER_REFERENCE],
                static::EDI_ORDER_DURST_CUSTOMER_REFERENCE => $payload[static::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE] ?? null,
            ]);

            if ($payload[static::PAYLOAD_ORDER_ITEMS] !== null) {
                $orderItems = [];

                foreach ($payload[static::PAYLOAD_ORDER_ITEMS] as $orderItem) {
                    $orderItems[] = [
                        static::EDI_GTIN => $orderItem[static::PAYLOAD_GTIN],
                        static::EDI_DURST_SKU => $orderItem[static::PAYLOAD_DURST_SKU],
                        static::EDI_PRODUCT_DESCRIPTION => $orderItem[static::PAYLOAD_PRODUCT_DESCRIPTION],
                        static::EDI_MERCHANT_SKU => $orderItem[static::PAYLOAD_MERCHANT_SKU],
                        static::EDI_QUANTITY => $orderItem[static::PAYLOAD_QUANTITY],
                        static::EDI_ORDER_ITEM_FK_SALES_ORDER => $orderItem[static::PAYLOAD_ORDER_ITEM_FK_SALES_ORDER] ?? null,
                        static::EDI_ORDER_ITEM_ORDER_REFERENCE => $orderItem[static::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE],
                        static::EDI_ORDER_ITEM_PRICE_TO_PAY => $orderItem[static::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY],
                    ];
                }

                $result[static::EDI_ORDER_ITEMS] = $orderItems;
            } else {
                $result[static::EDI_ORDER_ITEMS] = $payload[static::PAYLOAD_ORDER_ITEMS];
            }
        }

        return $result;
    }
}
