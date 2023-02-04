<?php


namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;

interface TourOrderInterface
{
    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersWithToursByDriver(DriverTransfer $driverTransfer): array;

    /**
     * @param int $idConcreteTour
     * @param int[] $excludedOrderIds
     * @return bool
     */
    public function hasConcreteTourOpenOrdersWithExcludedIds(int $idConcreteTour, array $excludedOrderIds): bool;

    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array;

    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return \Generated\Shared\Transfer\DriverAppTourTransfer[]
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array;
}
