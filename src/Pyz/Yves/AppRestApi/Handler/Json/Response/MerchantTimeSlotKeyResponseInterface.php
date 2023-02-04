<?php
/**
 * Durst - project - MerchantTimeSlotKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-24
 * Time: 12:46
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface MerchantTimeSlotKeyResponseInterface
{
    public const KEY_TIME_SLOTS = 'time_slots';
    public const KEY_TIME_SLOT_ID = 'time_slot_id';
    public const KEY_TIME_SLOT_MERCHANT_ID = 'merchant_id';
    public const KEY_TIME_SLOT_FROM = 'from';
    public const KEY_TIME_SLOT_TO = 'to';
    public const KEY_TIME_SLOT_TIME_SLOT_STRING = 'time_slot_string';
    public const KEY_TIME_SLOT_START_RAW = 'time_slot_start_raw';
    public const KEY_TIME_SLOT_END_RAW = 'time_slot_end_raw';
    public const KEY_TIME_SLOT_CURRENCY = 'currency';
    public const KEY_TIME_SLOT_TOTAL_DELIVERY_COST = 'total_delivery_cost';
    public const KEY_TIME_SLOT_TOTAL_MIN_VALUE = 'total_missing_min_value';
    public const KEY_TIME_SLOT_TOTAL_MIN_UNITS = 'total_missing_min_units';
    public const KEY_TIME_SLOT_USE_BRANCH_HP_KEY = 'use_branch_hp_key';
    public const KEY_TIME_SLOT_MESSAGE = 'message';
    public const KEY_TIME_SLOT_VALIDITY = 'validity';
    public const KEY_TIME_SLOT_CODE = 'code';

}
