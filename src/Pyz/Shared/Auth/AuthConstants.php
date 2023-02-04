<?php


namespace Pyz\Shared\Auth;

use Spryker\Shared\Auth\AuthConstants as SprykerAuthConstants;

interface AuthConstants extends SprykerAuthConstants
{
    public const JWT_ISSUER = 'JWT_ISSUER';
    public const JWT_AUDIENCE = 'JWT_AUDIENCE';
}