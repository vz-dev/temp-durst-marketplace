<?php


namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverCloseOrderResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;
    public const KEY_ORDER_CLOSED = 'order_closed';
    public const KEY_ORDER_ALREADY_CLOSED = 'already_closed';
    public const KEY_ERROR_MESSAGE = 'error_message';
}
