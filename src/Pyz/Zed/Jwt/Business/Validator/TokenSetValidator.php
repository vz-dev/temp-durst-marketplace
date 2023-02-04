<?php
/**
 * Durst - project - TokenSetValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 14:16
 */

namespace Pyz\Zed\Jwt\Business\Validator;

use Generated\Shared\Transfer\JwtTransfer;
use Pyz\Zed\Jwt\Business\Exception\JwtTokenNotSetException;

/**
 * Class TokenSetValidator
 * @package Pyz\Zed\Jwt\Business\Validator
 */
class TokenSetValidator extends BaseJwtValidator
{
    protected const ERROR_CODE = '100001';
    protected const ERROR_MESSAGE = 'Es wurde noch kein Token erstellt.';

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     * @throws \Pyz\Zed\Jwt\Business\Exception\JwtTokenNotSetException
     */
    public function isValid(JwtTransfer $jwtTransfer): bool
    {
        if ($jwtTransfer->getToken() === null) {
            throw new JwtTokenNotSetException();
        }

        return true;
    }
}
