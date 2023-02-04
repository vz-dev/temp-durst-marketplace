<?php
/**
 * Durst - project - OverviewKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 13:52
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface OverviewKeyResponseInterface
{
    public const KEY_TIME_SLOT = 'time_slot';
    public const KEY_TIME_SLOT_ID = 'time_slot_id';
    public const KEY_TIME_SLOT_MERCHANT_ID = 'merchant_id';
    public const KEY_TIME_SLOT_FROM = 'from';
    public const KEY_TIME_SLOT_TO = 'to';
    public const KEY_TIME_SLOT_TIME_SLOT_STRING = 'time_slot_string';
    public const KEY_TIME_SLOT_START_RAW = 'time_slot_start_raw';
    public const KEY_TIME_SLOT_END_RAW = 'time_slot_end_raw';
    public const KEY_TIME_SLOT_MESSAGE = 'message';

    public const KEY_TOTALS = 'totals';
    public const KEY_TOTALS_SUBTOTAL = 'subtotal';
    public const KEY_TOTALS_EXPENSE = 'expense';
    public const KEY_TOTALS_DISCOUNT = 'discount';
    public const KEY_TOTALS_TAX = 'tax';
    public const KEY_TOTALS_GRAND = 'grand';
    public const KEY_TOTALS_NET = 'net';
    public const KEY_TOTALS_DELIVERY_COST = 'delivery_cost';
    public const KEY_TOTALS_MISSING_MIN_AMOUNT = 'missing_min_amount';
    public const KEY_TOTALS_MISSING_MIN_UNITS = 'missing_min_units';
    public const KEY_TOTAL_DEPOSIT = 'deposit';
    public const KEY_TOTALS_WEIGHT = 'weight';
    public const KEY_TOTALS_DISPLAY = 'display';
    public const KEY_TOTALS_GROSS_SUBTOTAL = 'gross_subtotal';

    public const KEY_EXPENSES = 'expenses';
    public const KEY_EXPENSES_EXPENSE_TYPE = 'expense_type';
    public const KEY_EXPENSES_UNIT_GROSS_PRICE = 'unit_gross_price';
    public const KEY_EXPENSES_SUM_GROSS_PRICE = 'sum_gross_price';
    public const KEY_EXPENSES_NAME = 'name';
    public const KEY_EXPENSES_TAX_RATE = 'tax_rate';
    public const KEY_EXPENSES_QUANTITY = 'quantity';
    public const KEY_EXPENSES_UNIT_PRICE = 'unit_price';
    public const KEY_EXPENSES_SUM_PRICE = 'sum_price';
    public const KEY_EXPENSES_UNIT_PRICE_TO_PAY_AGGREGATION = 'unit_price_to_pay_aggregation';
    public const KEY_EXPENSES_SUM_PRICE_TO_PAY_AGGREGATION = 'sum_price_to_pay_aggregation';

    public const KEY_CART_ITEMS = 'cart_items';
    public const KEY_CART_ITEMS_NAME = 'name';
    public const KEY_CART_ITEMS_UNIT_NAME = 'unit_name';
    public const KEY_CART_ITEMS_UNIT_GROSS_PRICE = 'unit_gross_price';
    public const KEY_CART_ITEMS_QUANTITY = 'quantity';
    public const KEY_CART_ITEMS_SUM_GROSS_PRICE = 'sum_gross_price';
    public const KEY_CART_ITEMS_TAX_RATE = 'tax_rate';
    public const KEY_CART_ITEMS_SKU = 'sku';
    public const KEY_CART_ITEMS_UNIT_PRICE_TO_PAY_AGGREGATION = 'unit_price_to_pay_aggregation';
    public const KEY_CART_ITEMS_SUM_PRICE_TO_PAY_AGGREGATION = 'sum_price_to_pay_aggregation';

    public const KEY_CART_ITEMS_DISCOUNTS = 'discounts';
    public const KEY_CART_ITEMS_DISCOUNTS_UNIT_AMOUNT = 'unit_amount';
    public const KEY_CART_ITEMS_DISCOUNTS_SUM_AMOUNT = 'sum_amount';
    public const KEY_CART_ITEMS_DISCOUNTS_DISPLAY_NAME = 'display_name';
    public const KEY_CART_ITEMS_DISCOUNTS_QUANTITY = 'quantity';
    public const KEY_CART_ITEMS_DISCOUNTS_DISCOUNT_NAME = 'discount_name';
}
