<?php


namespace Pyz\Zed\Auth\Business\Exception;


class JwtTokenNotGeneratedException extends AuthException
{
    public const MESSAGE = 'Unable to generate token for driver "%s".';
}