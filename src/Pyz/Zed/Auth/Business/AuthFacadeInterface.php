<?php


namespace Pyz\Zed\Auth\Business;

use Generated\Shared\Transfer\DriverTransfer;
use Spryker\Zed\Auth\Business\AuthFacadeInterface as SprykerAuthFacadeInterface;

interface AuthFacadeInterface extends SprykerAuthFacadeInterface
{

    /**
     * Check, if a driver is allowed by checking the credentials
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     */
    public function driverLogin(string $email, string $password): string;

    /**
     * Checks, if a driver identified by his token is allowed to use further webservices
     *
     * @param string $token
     * @return bool
     */
    public function isDriverAuthenticated(string $token): bool;

    /**
     * Make driver token invalid by setting its state to deleted
     *
     * @param string $token
     * @return void
     */
    public function driverLogout(string $token): void;

    /**
     * Get a specific driver identified by his token
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByToken(string $token): DriverTransfer;
}