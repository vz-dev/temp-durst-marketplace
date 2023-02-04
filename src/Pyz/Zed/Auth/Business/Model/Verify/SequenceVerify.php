<?php


namespace Pyz\Zed\Auth\Business\Model\Verify;


use Lcobucci\JWT\Parser;
use Pyz\Zed\Auth\Business\Model\Jwt;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;

class SequenceVerify implements VerifyInterface
{
    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $parser;

    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * SequenceVerify constructor.
     * @param \Lcobucci\JWT\Parser $parser
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     */
    public function __construct(
        Parser $parser,
        DriverFacadeInterface $driverFacade
    )
    {
        $this->parser = $parser;
        $this->driverFacade = $driverFacade;
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

        $sequence = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_SEQUENCE_NUMBER);

        $idDriver = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_ID_DRIVER);

        $authEntity = $this
            ->driverFacade
            ->getDriverById($idDriver);

        if ($authEntity->getIdDriver() !== null) {
            return ($authEntity->getCurrentSequence() === $sequence);
        }

        return false;
    }
}