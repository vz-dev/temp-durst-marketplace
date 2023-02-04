<?php


namespace Pyz\Zed\Auth\Business\Exception;


class JwtTokenInvalidArgumentException extends AuthException
{
    public const MESSAGE = 'No driver found inside token.';
}