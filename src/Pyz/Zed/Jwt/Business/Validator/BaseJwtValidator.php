<?php
/**
 * Durst - project - BaseJwtValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 14:18
 */

namespace Pyz\Zed\Jwt\Business\Validator;

use Generated\Shared\Transfer\JwtTransfer;

/**
 * Class BaseJwtValidator
 * @package Pyz\Zed\Jwt\Business\Validator
 */
class BaseJwtValidator implements JwtValidatorInterface
{
    protected const ERROR_MESSAGE = '';
    protected const ERROR_CODE = 0;

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     */
    public function isValid(JwtTransfer $jwtTransfer): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return static::ERROR_MESSAGE;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return static::ERROR_CODE;
    }
}
