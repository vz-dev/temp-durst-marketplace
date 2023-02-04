<?php
/**
 * Durst - project - JwtSignNotMatchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 16:07
 */

namespace Pyz\Zed\Jwt\Business\Exception;

/**
 * Class JwtSignNotMatchException
 * @package Pyz\Zed\Jwt\Business\Exception
 */
class JwtSignNotMatchException extends JwtException
{
    public $code = '100003';
    public $message = 'Sign stimmt nicht mit Token überein.';
}
