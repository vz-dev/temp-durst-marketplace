<?php


namespace Pyz\Client\Deposit;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class DepositClient
 * @package Pyz\Client\Deposit
 * @method DepositFactory getFactory()
 */
class DepositClient extends AbstractClient implements DepositClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDeposits(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createDepositStub()
            ->getDeposits($requestTransfer);
    }
}