<?php
/**
 * Durst - project - JwtBasicValidationException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 15:34
 */

namespace Pyz\Zed\Jwt\Business\Exception;

/**
 * Class JwtBasicValidationException
 * @package Pyz\Zed\Jwt\Business\Exception
 */
class JwtBasicValidationException extends JwtException
{
    public $code = '100002';
    public $message = 'Die Basisvalidierung ist bereits fehlgeschlagen.';
}
