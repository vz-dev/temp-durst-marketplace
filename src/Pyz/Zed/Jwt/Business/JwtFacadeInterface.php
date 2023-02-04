<?php
/**
 * Durst - project - JwtFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 12:04
 */

namespace Pyz\Zed\Jwt\Business;

use Generated\Shared\Transfer\JwtTransfer;
use Lcobucci\JWT\Token;

/**
 * Interface JwtFacadeInterface
 * @package Pyz\Zed\Jwt\Business
 */
interface JwtFacadeInterface
{
    /**
     * Get a JWT transfer object and return it with the token set
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function prepareToken(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * Parse the token from the JWT transfer and return it
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(
        JwtTransfer $jwtTransfer
    ): Token;

    /**
     * Validate the given JWT transfer
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function validateJwt(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * Check, if the given sign was used to sign the token in the JWT transfer
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @param string $sign
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function verifyJwt(
        JwtTransfer $jwtTransfer,
        string $sign
    ): JwtTransfer;

    /**
     * Check, if the given sign was used to sign the given token
     *
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool;

    /**
     * Create a JWT transfer from the given token
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function tokenToTransfer(
        string $token
    ): JwtTransfer;
}
