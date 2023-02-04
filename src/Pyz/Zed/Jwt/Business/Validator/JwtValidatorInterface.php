<?php
/**
 * Durst - project - JwtValidatorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 13:57
 */

namespace Pyz\Zed\Jwt\Business\Validator;

use Generated\Shared\Transfer\JwtTransfer;

/**
 * Interface JwtValidatorInterface
 * @package Pyz\Zed\Jwt\Business\Validator
 */
interface JwtValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     */
    public function isValid(JwtTransfer $jwtTransfer): bool;

    /**
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * @return string
     */
    public function getErrorCode(): string;
}
