<?php
/**
 * Durst - project - JwtFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 12:05
 */

namespace Pyz\Zed\Jwt\Business;

use Generated\Shared\Transfer\JwtTransfer;
use Lcobucci\JWT\Token;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class JwtFacade
 * @package Pyz\Zed\Jwt\Business
 *
 * @method JwtBusinessFactory getFactory()
 */
class JwtFacade extends AbstractFacade implements JwtFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function prepareToken(
        JwtTransfer $jwtTransfer
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->prepareToken(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(
        JwtTransfer $jwtTransfer
    ): Token
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->getParsedToken(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function validateJwt(
        JwtTransfer $jwtTransfer
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->validateJwt(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @param string $sign
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function verifyJwt(
        JwtTransfer $jwtTransfer,
        string $sign
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->verifyJwt(
                $jwtTransfer,
                $sign
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->verifySignByToken(
                $token,
                $sign
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function tokenToTransfer(
        string $token
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createJwtModel()
            ->tokenToTransfer(
                $token
            );
    }
}
