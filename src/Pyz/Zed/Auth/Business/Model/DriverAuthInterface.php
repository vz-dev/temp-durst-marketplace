<?php


namespace Pyz\Zed\Auth\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;

interface DriverAuthInterface
{
    /**
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Pyz\Zed\Auth\Business\Exception\DriverEmailNotFoundException
     * @throws \Pyz\Zed\Auth\Business\Exception\JwtTokenNotGeneratedException
     */
    public function authenticate(string $email, string $password): string;

    /**
     * @param string $token
     * @return bool
     */
    public function isAuthorized(string $token): bool;

    /**
     * @param string $token
     * @return void
     */
    public function logout(string $token): void;

    /**
     * @param string $token
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    public function getDriverByToken(string $token): DriverTransfer;
}