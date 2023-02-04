<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:03
 */

namespace Pyz\Shared\Edifact;


interface EdifactConstants
{
    public const EDIFACT_EXPORT_TYPE_ORDER = 'ORDERS';
    public const EDIFACT_EXPORT_TYPE_DEPOSIT = 'DEPOSIT';
    public const EDIFACT_EXPORT_TYPE_NON_EDI = 'NON-EDI';

    public const EDIFACT_EXPORT_VERSION_1 = 'v1';
    public const EDIFACT_EXPORT_VERSION_2 = 'v2';

    public const EDIFACT_EXPORT_VERSION_DEFAULT = self::EDIFACT_EXPORT_VERSION_1;

    public const VALID_EDIFACT_EXPORT_VERSIONS = [
        self::EDIFACT_EXPORT_VERSION_1,
        self::EDIFACT_EXPORT_VERSION_2,
    ];

    public const EDIFACT_EXPORT_VERSION_CHOICES = [
        self::EDIFACT_EXPORT_VERSION_1 => self::EDIFACT_EXPORT_VERSION_1,
        self::EDIFACT_EXPORT_VERSION_2 => self::EDIFACT_EXPORT_VERSION_2,
    ];

    public const EDIFACT_EXCLUDE_MISSING_ITEM_RETURNS_DEFAULT = false;

    /** @var int Status code indicating a successful transfer */
    public const STATUS_CODE_SUCCESS = 200;

    /** @var string Message indicating a successful transfer */
    public const MESSAGE_SUCCESS = 'EDI message transferred successfully to the merchant client';
}
