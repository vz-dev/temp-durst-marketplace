<?php
/**
 * Durst - project - JwtTokenNotSetException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 14:14
 */

namespace Pyz\Zed\Jwt\Business\Exception;

/**
 * Class JwtTokenNotSetException
 * @package Pyz\Zed\Jwt\Business\Exception
 */
class JwtTokenNotSetException extends JwtException
{
    public $code = '100001';
    public $message = 'Es wurde noch kein Token erstellt.';
}
