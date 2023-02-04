<?php


namespace Pyz\Zed\Auth\Business\Exception;


class DriverEmailNotFoundException extends AuthException
{
    public const MESSAGE = 'A driver with the email "%s" does not exist.';
}