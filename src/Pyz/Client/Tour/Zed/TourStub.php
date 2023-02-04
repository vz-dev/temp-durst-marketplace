<?php


namespace Pyz\Client\Tour\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class TourStub implements TourStubInterface
{
    protected const URL_DRIVER_APP_TOUR = '/tour/gateway/get-tours-for-driver';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * TourStub constructor.
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getToursWithOrders(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_DRIVER_APP_TOUR,
                $requestTransfer,
                null
            );
    }
}
