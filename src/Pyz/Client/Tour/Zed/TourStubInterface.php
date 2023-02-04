<?php


namespace Pyz\Client\Tour\Zed;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface TourStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function getToursWithOrders(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;
}