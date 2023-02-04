<?php


namespace Pyz\Zed\Auth\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;

interface JwtNumberGeneratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return string
     */
    public function generateDriverTokenNumber(DriverTransfer $driverTransfer): string;
}