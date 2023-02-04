<?php
/**
 * Durst - project - JwtBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 11:42
 */

namespace Pyz\Zed\Jwt\Business;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Pyz\Zed\Jwt\Business\Model\JwtInterface;
use Pyz\Zed\Jwt\Business\Model\JwtModel;
use Pyz\Zed\Jwt\Business\Validator\TokenSetValidator;
use Pyz\Zed\Jwt\JwtConfig;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class JwtBusinessFactory
 * @package Pyz\Zed\Jwt\Business
 *
 * @method JwtConfig getConfig()
 */
class JwtBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Jwt\Business\Model\JwtInterface
     */
    public function createJwtModel(): JwtInterface
    {
        return new JwtModel(
            $this
                ->createJwtBuilder(),
            $this
                ->createJwtParser(),
            $this
                ->createJwtSigner(),
            $this
                ->getConfig(),
            $this
                ->createBaseValidators()
        );
    }

    /**
     * @return \Lcobucci\JWT\Builder
     */
    protected function createJwtBuilder(): Builder
    {
        return new Builder();
    }

    /**
     * @return \Lcobucci\JWT\Parser
     */
    protected function createJwtParser(): Parser
    {
        return new Parser();
    }

    /**
     * @return \Lcobucci\JWT\Signer\Hmac\Sha512
     */
    protected function createJwtSigner(): Sha512
    {
        return new Sha512();
    }

    /**
     * @return array|\Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface[]
     */
    protected function createBaseValidators(): array
    {
        return [
            new TokenSetValidator()
        ];
    }
}
