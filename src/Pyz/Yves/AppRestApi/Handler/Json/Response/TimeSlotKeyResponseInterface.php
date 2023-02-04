<?php
/**
 * Durst - project - TimeSlotKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface TimeSlotKeyResponseInterface
{
    public const KEY_TIME_SLOTS = 'time_slots';
    public const KEY_TIME_SLOT_ID = 'time_slot_id';
    public const KEY_TIME_SLOT_MERCHANT_ID = 'merchant_id';
    public const KEY_TIME_SLOT_FROM = 'from';
    public const KEY_TIME_SLOT_TO = 'to';
    public const KEY_TIME_SLOT_TIME_SLOT_STRING = 'time_slot_string';
    public const KEY_TIME_SLOT_USE_BRANCH_HP_KEY = 'use_branch_hp_key';
    public const KEY_TIME_SLOT_CHEAPEST_SLOT = 'cheapest_slot';
    public const KEY_TIME_SLOT_TOTAL_CART = 'total_cart';
    public const KEY_TIME_SLOT_TOTAL_DELIVERY_COST = 'total_delivery_cost';
    public const KEY_TIME_SLOT_TOTAL_DEPOSIT = 'total_deposit';
    public const KEY_TIME_SLOT_TOTAL_DISCOUNT = 'total_discount';
    public const KEY_TIME_SLOT_TOTAL_TAXES = 'total_included_taxes';
    public const KEY_TIME_SLOT_TOTAL_MIN_VALUE = 'total_missing_min_value';
    public const KEY_TIME_SLOT_TOTAL_MIN_UNITS = 'total_missing_min_units';
    public const KEY_TIME_SLOT_TOTAL = 'total';
    public const KEY_TIME_SLOT_CURRENCY = 'currency';
    public const KEY_TIME_SLOT_TOTAL_NET = 'total_net';
    public const KEY_TIME_SLOT_TOTAL_EXPENSE = 'total_expense';
    public const KEY_TIME_SLOT_TOTAL_GROSS_SUBTOTAL = 'total_gross_subtotal';
    public const KEY_TIME_SLOT_TOTAL_DISPLAY = 'total_display';

    public const KEY_DURST_TERMS = 'durst_terms';
    public const KEY_DURST_TERMS_TEXT = 'text';
    public const KEY_DURST_TERMS_HINT_TEXT = 'hint_text';
    public const KEY_DURST_TERMS_BUTTON_TEXT = 'button_text';
}
