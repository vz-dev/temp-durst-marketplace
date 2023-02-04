<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface DriverLoginRequestInterface extends DriverAppRequestInterface
{
    public const KEY_EMAIL = 'email';
    public const KEY_PASSWORD = 'password';
}
