<?php


namespace Pyz\Client\Deposit\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface DepositStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function getDeposits(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;
}