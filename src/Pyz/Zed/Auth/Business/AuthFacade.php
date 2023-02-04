<?php


namespace Pyz\Zed\Auth\Business;

use Generated\Shared\Transfer\DriverTransfer;
use Spryker\Zed\Auth\Business\AuthFacade as SprykerAuthFacade;

/**
 * Class AuthFacade
 * @package Pyz\Zed\Auth\Business
 * @method AuthBusinessFactory getFactory()
 */
class AuthFacade extends SprykerAuthFacade implements AuthFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function driverLogin(string $email, string $password): string
    {
        return $this
            ->getFactory()
            ->createDriverAuthModel()
            ->authenticate($email, $password);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isDriverAuthenticated(string $token): bool
    {
        return $this
            ->getFactory()
            ->createDriverAuthModel()
            ->isAuthorized($token);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function driverLogout(string $token): void
    {
        $this
            ->getFactory()
            ->createDriverAuthModel()
            ->logout($token);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\DriverTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDriverByToken(string $token): DriverTransfer
    {
        return $this
            ->getFactory()
            ->createDriverAuthModel()
            ->getDriverByToken($token);
    }
}