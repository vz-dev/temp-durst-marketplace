<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;

interface DriverGtinRequestInterface
{
    public const KEY_TOKEN = DriverLoginResponseInterface::KEY_TOKEN;
}