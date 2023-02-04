<?php


namespace Pyz\Client\Deposit;

use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface DepositClientInterface
{
    /**
     * Return a list of all deposits
     * .
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function getDeposits(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;
}
