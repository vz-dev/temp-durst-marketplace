<?php


namespace Pyz\Client\Tour;


use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class TourClient
 * @package Pyz\Client\Tour
 * @method TourFactory getFactory()
 */
class TourClient extends AbstractClient implements TourClientInterface
{

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\DriverAppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getToursWithOrders(DriverAppApiRequestTransfer $requestTransfer): DriverAppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createTourStub()
            ->getToursWithOrders($requestTransfer);
    }
}