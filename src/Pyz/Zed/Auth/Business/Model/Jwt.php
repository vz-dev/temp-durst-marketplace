<?php


namespace Pyz\Zed\Auth\Business\Model;


use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\DriverTransfer;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Token;
use Pyz\Zed\Auth\AuthConfig;

class Jwt implements JwtInterface
{
    protected const JWT_TIME_FORMAT = 'U';
    protected const JWT_NOT_BEFORE_FORMAT = '%s %s';
    protected const JWT_EXPIRATION_FORMAT = '%s %s %s';

    public const JWT_SUBJECT_FORMAT = '%s %s';

    public const JWT_CLAIM_EMAIL = 'jti';
    public const JWT_CLAIM_SEQUENCE_NUMBER = 'sequence';
    public const JWT_CLAIM_ID_DRIVER = 'idDriver';

    /**
     * @var \Lcobucci\JWT\Builder
     */
    protected $builder;

    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $parser;

    /**
     * @var \Lcobucci\JWT\Signer\Hmac\Sha512
     */
    protected $signer;

    /**
     * @var \Pyz\Zed\Auth\AuthConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Auth\Business\Model\JwtNumberGeneratorInterface
     */
    protected $jwtNumberGenerator;

    /**
     * @var array
     */
    protected $verifiers;

    /**
     * Jwt constructor.
     * @param \Lcobucci\JWT\Builder $builder
     * @param \Lcobucci\JWT\Parser $parser
     * @param \Lcobucci\JWT\Signer\Hmac\Sha512 $signer
     * @param \Pyz\Zed\Auth\AuthConfig $config
     * @param \Pyz\Zed\Auth\Business\Model\JwtNumberGeneratorInterface $jwtNumberGenerator
     * @param array $verifiers
     */
    public function __construct(
        Builder $builder,
        Parser $parser,
        Sha512 $signer,
        AuthConfig $config,
        JwtNumberGeneratorInterface $jwtNumberGenerator,
        array $verifiers
    )
    {
        $this->builder = $builder;
        $this->parser = $parser;
        $this->signer = $signer;
        $this->config = $config;
        $this->jwtNumberGenerator = $jwtNumberGenerator;
        $this->verifiers = $verifiers;
    }

    /**
     * @param string $email
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return string
     * @throws \Exception
     */
    public function generateToken(string $email, DriverTransfer $driverTransfer): string
    {
        return $this
            ->builder
            ->setIssuer($this->config->getJwtIssuer())
            ->setAudience($this->config->getJwtAudience())
            ->setId($driverTransfer->getEmail())
            ->setIssuedAt($this->getIssuedAt())
            ->setNotBefore($this->getNotBefore())
            ->setExpiration($this->getExpiration())
            ->setSubject(sprintf(
                static::JWT_SUBJECT_FORMAT,
                $driverTransfer->getFirstName(),
                $driverTransfer->getLastName()
            ))
            ->set(
                static::JWT_CLAIM_SEQUENCE_NUMBER,
                $this->jwtNumberGenerator->generateDriverTokenNumber($driverTransfer)
            )
            ->set(
                static::JWT_CLAIM_ID_DRIVER,
                $driverTransfer->getIdDriver()
            )
            ->sign(
                $this->signer,
                $driverTransfer->getEmail()
            )
            ->getToken();
    }

    /**
     * @param string $token
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(string $token): Token
    {
        return $this
            ->parser
            ->parse($token);
    }

    /**
     * @param string $token
     * @return string
     */
    public function getEmailFromToken(string $token): string
    {
        $parsedToken = $this
            ->getParsedToken($token);

        return $parsedToken
            ->getClaim(static::JWT_CLAIM_EMAIL);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isAuthorized(string $token): bool
    {
        foreach ($this->verifiers as $verifier) {
            if ($verifier->verify($token) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getIssuedAt(): string
    {
        $timezone = new DateTimeZone($this->config->getProjectTimeZone());

        return (new DateTime('now'))
            ->setTimezone($timezone)
            ->format(static::JWT_TIME_FORMAT);
    }

    /**
     * @return string
     */
    protected function getNotBefore(): string
    {
        return strtotime(
            sprintf(
                static::JWT_NOT_BEFORE_FORMAT,
                'now',
                'midnight'
            )
        );
    }

    /**
     * @return string
     */
    protected function getExpiration(): string
    {
        return strtotime(
            sprintf(
                static::JWT_EXPIRATION_FORMAT,
                'now',
                '+1day',
                'midnight'
            )
        );
    }
}