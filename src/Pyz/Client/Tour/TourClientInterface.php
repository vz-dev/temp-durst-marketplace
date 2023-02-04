<?php


namespace Pyz\Client\Tour;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;

interface TourClientInterface
{
    /**
     * Fetch a list of all tours for today from a single merchant identified by its PIN
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     */
    public function getToursWithOrders(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer;
}