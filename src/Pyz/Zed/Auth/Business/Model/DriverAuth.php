<?php


namespace Pyz\Zed\Auth\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;
use Lcobucci\JWT\Token;
use Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException;
use Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;

class DriverAuth implements DriverAuthInterface
{

    protected const AUTH_DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    /**
     * @var \Pyz\Zed\Auth\Business\Model\JwtInterface
     */
    protected $jwt;

    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * DriverAuth constructor.
     * @param \Pyz\Zed\Auth\Business\Model\JwtInterface $jwt
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     */
    public function __construct(
        JwtInterface $jwt,
        DriverFacadeInterface $driverFacade
    )
    {
        $this->jwt = $jwt;
        $this->driverFacade = $driverFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     */
    public function authenticate(string $email, string $password): string
    {
        $driver = $this
            ->getDriverByEmail($email);

        if ($driver->getIdDriver() === null) {
            throw new DriverEmailNotFoundException(sprintf(
                DriverEmailNotFoundException::MESSAGE,
                $email
            ));
        }

        if (password_verify($password, $driver->getPassword()) === true) {
            $token = $this
                ->generateToken($email);

            $this
                ->setDriverLoggedIn($token);

            return $token;
        }

        throw new JwtTokenNotGeneratedException(sprintf(
            JwtTokenNotGeneratedException::MESSAGE,
            $email
        ));
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return bool
     */
    public function isAuthorized(string $token): bool
    {
        return $this
            ->jwt
            ->isAuthorized($token);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     */
    public function logout(string $token): void
    {
        $parsedToken = $this
            ->getParsedToken($token);

        $email = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_EMAIL);

        $driver = $this
            ->getDriverByEmail($email);

        $driver
            ->setCurrentSequence(null);

        $this
            ->driverFacade
            ->updateDriver($driver);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByToken(string $token): DriverTransfer
    {
        $email = $this
            ->getEmailFromToken($token);

        return $this
            ->getDriverByEmail($email);
    }

    /**
     * @param string $email
     * @return string
     * @throws \Exception
     */
    protected function generateToken(string $email): string
    {
        $driverTransfer = $this
            ->getDriverByEmail($email);

        return $this
            ->jwt
            ->generateToken(
                $email,
                $driverTransfer
            );
    }

    /**
     * @param string $email
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function getDriverByEmail(string $email): DriverTransfer
    {
        return $this
            ->driverFacade
            ->getDriverByEmail($email);
    }

    /**
     * @param string $token
     * @return \Lcobucci\JWT\Token
     */
    protected function getParsedToken(string $token): Token
    {
        return $this
            ->jwt
            ->getParsedToken($token);
    }

    /**
     * @param string $token
     * @return string
     */
    protected function getEmailFromToken(string $token): string
    {
        return $this
            ->jwt
            ->getEmailFromToken($token);
    }

    /**
     * @param string $token
     */
    protected function setDriverLoggedIn(string $token): void
    {
        $parsedToken = $this
            ->getParsedToken($token);

        $email = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_EMAIL);

        $sequence = $parsedToken
            ->getClaim(Jwt::JWT_CLAIM_SEQUENCE_NUMBER);

        $driver = $this
            ->getDriverByEmail($email);

        $driver
            ->setCurrentSequence($sequence);

        $this
            ->driverFacade
            ->updateDriver($driver);
    }
}