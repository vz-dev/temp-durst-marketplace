<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;

interface DriverCloseOrderRequestInterface extends DriverAppRequestInterface
{
    public const KEY_TOKEN = DriverLoginResponseInterface::KEY_TOKEN;

    public const KEY_ORDER_ID = 'order_id';
    public const KEY_IS_RESELLER = 'is_reseller';
    public const KEY_SIGNED_AT = 'signed_at';

    public const KEY_RETURNED_DEPOSITS = 'returned_deposits';
    public const KEY_RETURNED_DEPOSITS_DEPOSIT_ID = 'deposit_id';
    public const KEY_RETURNED_DEPOSITS_DEPOSIT = 'deposit';
    public const KEY_RETURNED_DEPOSITS_CASES = 'cases';
    public const KEY_RETURNED_DEPOSITS_BOTTLES = 'bottles';

    public const KEY_ORDER_ITEMS = 'order_items';
    public const KEY_ORDER_ITEMS_ORDER_ITEM_ID = 'order_item_id';
    public const KEY_ORDER_ITEMS_QUANTITY = 'quantity';
    public const KEY_ORDER_ITEMS_STATUS = 'status';

    public const KEY_SIGNATURE_IMAGE = 'signature_image';

    public const KEY_VOUCHER = 'voucher';
    public const KEY_VOUCHER_SALES_DISCOUNT_ID = 'sales_discount_id';
    public const KEY_VOUCHER_AMOUNT = 'amount';

    public const KEY_EXTERNAL_AMOUNT_PAID = 'external_amount_paid';
}
