<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverDepositResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;
    public const KEY_IS_UPDATABLE = DriverLoginResponseInterface::KEY_IS_UPDATABLE;
    public const KEY_DEPOSIT_ENTRIES = 'deposit_entries';
    public const KEY_DEPOSIT_ENTRIES_ID = 'deposit_id';
    public const KEY_DEPOSIT_ENTRIES_NAME = 'name';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT = 'deposit';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT_B2B = 'deposit_b2b';
    public const KEY_DEPOSIT_ENTRIES_CODE = 'code';
    public const KEY_DEPOSIT_ENTRIES_BOTTLES = 'bottles';
    public const KEY_DEPOSIT_ENTRIES_MATERIAL = 'material';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT_CASE = 'deposit_case';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT_CASE_B2B = 'deposit_case_b2b';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT_PER_BOTTLE = 'deposit_per_bottle';
    public const KEY_DEPOSIT_ENTRIES_DEPOSIT_PER_BOTTLE_B2B = 'deposit_per_bottle_b2b';
    public const KEY_DEPOSIT_ENTRIES_VOLUME_PER_BOTTLE = 'volume_per_bottle';
    public const KEY_DEPOSIT_ENTRIES_PRESENTATION_NAME = 'presentation_name';
    public const KEY_DEPOSIT_ENTRIES_WEIGHT = 'weight';
    public const KEY_GTINS = 'gtins';
}
