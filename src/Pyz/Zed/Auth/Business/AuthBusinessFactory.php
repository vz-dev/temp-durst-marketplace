<?php


namespace Pyz\Zed\Auth\Business;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\ValidationData;
use Pyz\Zed\Auth\AuthConfig;
use Pyz\Zed\Auth\AuthDependencyProvider;
use Pyz\Zed\Auth\Business\Model\DriverAuth;
use Pyz\Zed\Auth\Business\Model\DriverAuthInterface;
use Pyz\Zed\Auth\Business\Model\Jwt;
use Pyz\Zed\Auth\Business\Model\JwtInterface;
use Pyz\Zed\Auth\Business\Model\JwtNumberGenerator;
use Pyz\Zed\Auth\Business\Model\JwtNumberGeneratorInterface;
use Pyz\Zed\Auth\Business\Model\Verify\DriverVerify;
use Pyz\Zed\Auth\Business\Model\Verify\SequenceVerify;
use Pyz\Zed\Auth\Business\Model\Verify\ValidationDataVerify;
use Pyz\Zed\Auth\Business\Model\Verify\VerifyInterface;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;
use Spryker\Zed\Auth\Business\AuthBusinessFactory as SprykerAuthBusinessFactory;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

/**
 * Class AuthBusinessFactory
 * @package Pyz\Zed\Auth\Business
 * @method AuthConfig getConfig()
 * @method AuthQueryContainerInterface getQueryContainer()
 */
class AuthBusinessFactory extends SprykerAuthBusinessFactory
{
    /**
     * @return \Pyz\Zed\Driver\Business\DriverFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDriverFacade(): DriverFacadeInterface
    {
        return $this
            ->getProvidedDependency(AuthDependencyProvider::FACADE_DRIVER);
    }

    /**
     * @return \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSequenceNumberFacade(): SequenceNumberFacadeInterface
    {
        return $this
            ->getProvidedDependency(AuthDependencyProvider::FACADE_SEQUENCE_NUMBER);
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
     * @return \Lcobucci\JWT\ValidationData
     */
    protected function createJwtValidationData(): ValidationData
    {
        return new ValidationData();
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\JwtNumberGeneratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createJwtDriverTokenReferenceGenerator(): JwtNumberGeneratorInterface
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getJwtDriverTokenReferenceDefaults();

        return new JwtNumberGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createJwtVerifierStack(): array
    {
        return [
            $this->createDriverVerify(),
            $this->createValidationDataVerify(),
            $this->createSequenceVerify()
        ];
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\Verify\VerifyInterface
     */
    protected function createDriverVerify(): VerifyInterface
    {
        return new DriverVerify(
            $this->createJwtParser(),
            $this->createJwtSigner()
        );
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\Verify\VerifyInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createValidationDataVerify(): VerifyInterface
    {
        return new ValidationDataVerify(
            $this->createJwtParser(),
            $this->createJwtValidationData(),
            $this->getDriverFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\Verify\VerifyInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createSequenceVerify(): VerifyInterface
    {
        return new SequenceVerify(
            $this->createJwtParser(),
            $this->getDriverFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\JwtInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createJwtModel(): JwtInterface
    {
        return new Jwt(
            $this->createJwtBuilder(),
            $this->createJwtParser(),
            $this->createJwtSigner(),
            $this->getConfig(),
            $this->createJwtDriverTokenReferenceGenerator(),
            $this->createJwtVerifierStack()
        );
    }

    /**
     * @return \Pyz\Zed\Auth\Business\Model\DriverAuthInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDriverAuthModel(): DriverAuthInterface
    {
        return new DriverAuth(
            $this->createJwtModel(),
            $this->getDriverFacade()
        );
    }
}