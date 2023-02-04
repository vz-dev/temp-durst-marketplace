<?php


namespace Pyz\Zed\Auth\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;
use Lcobucci\JWT\Token;

interface JwtInterface
{
    /**
     * @param string $email
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return string
     */
    public function generateToken(string $email, DriverTransfer $driverTransfer): string;

    /**
     * @param string $token
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(string $token): Token;

    /**
     * @param string $token
     * @return string
     */
    public function getEmailFromToken(string $token): string;

    /**
     * @param string $token
     * @return bool
     */
    public function isAuthorized(string $token): bool;
}