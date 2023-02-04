<?php
/**
 * Durst - project - JwtConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 11:46
 */

namespace Pyz\Shared\Jwt;

interface JwtConstants
{
    public const JWT_TIME_FORMAT = 'U';
    public const TRANSFER_TIME_FORMAT = 'Y-m-d H:i:s';

    public const KEY_AUDIENCE = 'aud';
    public const KEY_EXPIRATION = 'exp';
    public const KEY_ID = 'jti';
    public const KEY_ISSUED_AT = 'iat';
    public const KEY_ISSUER = 'iss';
    public const KEY_NOT_BEFORE = 'nbf';
    public const KEY_SUBJECT = 'sub';
}
