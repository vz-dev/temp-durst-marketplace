<?php


namespace Pyz\Zed\Auth\Business\Model\Verify;


interface VerifyInterface
{
    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool;
}