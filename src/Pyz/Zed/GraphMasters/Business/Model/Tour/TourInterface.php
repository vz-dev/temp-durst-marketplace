<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

interface TourInterface
{
    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @return GraphMastersTourTransfer
     *
     * @throws PropelException
     */
    public function save(GraphMastersTourTransfer $tourTransfer): GraphMastersTourTransfer;

    /**
     * @param DstGraphmastersTour $tourEntity
     *
     * @return GraphMastersTourTransfer
     */
    public function entityToTransfer(DstGraphmastersTour $tourEntity): GraphMastersTourTransfer;

    /**
     * @param int $idTour
     *
     * @return GraphMastersTourTransfer
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourById(int $idTour): GraphMastersTourTransfer;

    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function comment(GraphMastersTourTransfer $tourTransfer): void;

    /**
     * @param DriverTransfer $driverTransfer
     *
     * @return array|DriverAppTourTransfer[]
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array;

    /**
     * @param string $tourReference
     *
     * @return GraphMastersTourTransfer
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourByReference(string $tourReference): GraphMastersTourTransfer;

    /**
     * @param string $originalId
     *
     * @return GraphMastersTourTransfer|null
     *
     * @throws PropelException
     */
    public function getTourByOriginalId(string $originalId): ?GraphMastersTourTransfer;

    /**
     * @param int $fkBranch
     *
     * @return array
     *
     * @throws PropelException
     */
    public function getTodaysIdleToursByFkBranch(int $fkBranch): array;

    /**
     * @param int $idTour
     *
     * @throws PropelException
     */
    public function deleteTourById(int $idTour): void;

    /**
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function fixOpenToursCutoffReached() : void;
}
