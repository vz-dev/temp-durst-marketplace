<?php
/**
 * Durst - project - JwtInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 11:37
 */

namespace Pyz\Zed\Jwt\Business\Model;

use Generated\Shared\Transfer\JwtTransfer;
use Lcobucci\JWT\Token;

/**
 * Interface JwtInterface
 * @package Pyz\Zed\Jwt\Business\Model
 */
interface JwtInterface
{
    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function prepareToken(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(
        JwtTransfer $jwtTransfer
    ): Token;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function validateJwt(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @param string $sign
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function verifyJwt(
        JwtTransfer $jwtTransfer,
        string $sign
    ): JwtTransfer;

    /**
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool;

    /**
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function tokenToTransfer(
        string $token
    ): JwtTransfer;
}
