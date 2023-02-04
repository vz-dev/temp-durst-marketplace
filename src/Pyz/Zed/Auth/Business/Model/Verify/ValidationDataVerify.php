<?php


namespace Pyz\Zed\Auth\Business\Model\Verify;


use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Pyz\Zed\Auth\AuthConfig;
use Pyz\Zed\Auth\Business\Model\Jwt;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;

class ValidationDataVerify implements VerifyInterface
{
    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $parser;

    /**
     * @var ValidationData
     */
    protected $validationData;

    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * @var \Pyz\Zed\Auth\AuthConfig
     */
    protected $config;

    /**
     * ValidationDataVerify constructor.
     * @param \Lcobucci\JWT\Parser $parser
     * @param \Lcobucci\JWT\ValidationData $validationData
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     * @param \Pyz\Zed\Auth\AuthConfig $config
     */
    public function __construct(
        Parser $parser,
        ValidationData $validationData,
        DriverFacadeInterface $driverFacade,
        AuthConfig $config
    )
    {
        $this->parser = $parser;
        $this->validationData = $validationData;
        $this->driverFacade = $driverFacade;
        $this->config = $config;
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

        $validationData = $this
            ->createValidationData($token);

        return $parsedToken
            ->validate($validationData);
    }

    /**
     * @param string $token
     * @return \Lcobucci\JWT\ValidationData
     */
    protected function createValidationData(string $token): ValidationData
    {
        $parsedToken = $this
            ->parser
            ->parse($token);

        $email = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_EMAIL);

        $driver = $this
            ->driverFacade
            ->getDriverByEmail($email);

        $validationData = $this
            ->validationData;

        $validationData
            ->setAudience($this->config->getJwtAudience());
        $validationData
            ->setCurrentTime(time());
        $validationData
            ->setId($driver->getEmail());
        $validationData
            ->setIssuer($this->config->getJwtIssuer());
        $validationData
            ->setSubject(sprintf(
                Jwt::JWT_SUBJECT_FORMAT,
                $driver->getFirstName(),
                $driver->getLastName()
            ));

        return $validationData;
    }
}