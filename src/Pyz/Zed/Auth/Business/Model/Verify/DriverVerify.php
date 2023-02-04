<?php


namespace Pyz\Zed\Auth\Business\Model\Verify;


use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Pyz\Zed\Auth\Business\Model\Jwt;

class DriverVerify implements VerifyInterface
{
    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $parser;

    /**
     * @var \Lcobucci\JWT\Signer\Hmac\Sha512
     */
    protected $signer;

    /**
     * DriverVerify constructor.
     * @param \Lcobucci\JWT\Parser $parser
     * @param \Lcobucci\JWT\Signer\Hmac\Sha512 $signer
     */
    public function __construct(
        Parser $parser,
        Sha512 $signer
    )
    {
        $this->parser = $parser;
        $this->signer = $signer;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool
    {
        $parsedToken = $this
            ->parser
            ->parse($token);

        $email = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_EMAIL);

        return $parsedToken
            ->verify(
                $this->signer,
                $email
            );
    }
}