<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverLoginResponseInterface
{
    public const KEY_AUTH_VALID = 'auth_valid';
    public const KEY_IS_UPDATABLE = 'is_updatable';
    public const KEY_FIRST_NAME = 'first_name';
    public const KEY_LAST_NAME = 'last_name';
    public const KEY_DATA_RETENTION_DAYS = 'data_retention_days';
    public const KEY_TOKEN = 'token';
}
