<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverLoginResponseInterface;

interface DriverDepositRequestInterface extends DriverAppRequestInterface
{
    public const KEY_TOKEN = DriverLoginResponseInterface::KEY_TOKEN;
}
