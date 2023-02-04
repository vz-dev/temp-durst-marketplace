<?php


namespace Pyz\Client\Deposit\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class DepositStub implements DepositStubInterface
{
    protected const URL_DRIVER_APP_DEPOSIT = '/deposit/gateway/get-all-deposits-for-driver-app';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * DepositStub constructor.
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }

    /**
     * @param DriverAppApiRequestTransfer $requestTransfer
     * @return DriverAppApiResponseTransfer|TransferInterface
     */
    public function getDeposits(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_DEPOSIT,
                $requestTransfer,
                null
            );
    }
}