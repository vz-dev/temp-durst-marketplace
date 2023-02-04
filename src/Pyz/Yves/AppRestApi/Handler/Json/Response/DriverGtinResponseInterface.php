<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverGtinResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;
    public const KEY_GTINS = 'gtins';
    public const KEY_GTINS_GTIN = 'gtin';
    public const KEY_GTINS_PRODUCT_NAME = 'product_name';
    public const KEY_GTINS_SKUS = 'skus';
    public const KEY_GTINS_SKU = 'sku';
    public const KEY_GTINS_DEPOSIT_ID = 'deposit_id';
}
