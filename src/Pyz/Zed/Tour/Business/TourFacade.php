<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 12:41
 */

namespace Pyz\Zed\Tour\Business;

use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourExportTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\DrivingLicenceTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\VehicleCategoryTransfer;
use Generated\Shared\Transfer\VehicleTransfer;
use Generated\Shared\Transfer\VehicleTypeTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Business\Parser\TourExportParser;
use Pyz\Zed\Tour\Business\Util\EdiDepositExportUtil;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method TourBusinessFactory getFactory()
 */
class TourFacade extends AbstractFacade implements TourFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function addAbstractTour(AbstractTourTransfer $abstractTourTransfer): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->save($abstractTourTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function updateAbstractTour(AbstractTourTransfer $abstractTourTransfer): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->save($abstractTourTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return array
     */
    public function getAbstractToursByFkBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->getAbstractToursByFkBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function getAbstractTourById(int $idAbstractTour): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->getAbstractTourById($idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourById(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->getConcreteTourById($idConcreteTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function commentConcreteTour(ConcreteTourTransfer $concreteTourTransfer): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->comment($concreteTourTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function activateAbstractTour(int $idAbstractTour): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->activate($idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function deactivateAbstractTour(int $idAbstractTour): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->deactivate($idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTourTransfer
     */
    public function createConcreteTourForConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->createConcreteTourForConcreteTimeSlot($concreteTimeSlotTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function deleteAbstractTour(int $idAbstractTour): AbstractTourTransfer
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->delete($idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function getVehicleTypeById(int $idVehicleType): VehicleTypeTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleTypeModel()
            ->getVehicleTypeById($idVehicleType);
    }

    /**
     * {@inheritdoc}
     *
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     */
    public function addVehicleType(VehicleTypeTransfer $vehicleTypeTransfer): VehicleTypeTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleTypeModel()
            ->save($vehicleTypeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     */
    public function updateVehicleType(VehicleTypeTransfer $vehicleTypeTransfer): VehicleTypeTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleTypeModel()
            ->save($vehicleTypeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return VehicleTypeTransfer[]
     */
    public function getVehicleTypesByFkBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createVehicleTypeModel()
            ->getVehicleTypesByFkBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @return VehicleCategoryTransfer[]
     */
    public function getActiveVehicleCategories(): array
    {
        return $this
            ->getFactory()
            ->createVehicleCategoryModel()
            ->getActiveVehicleCategories();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function removeVehicleType(int $idVehicleType): VehicleTypeTransfer
    {
        return $this->getFactory()
            ->createVehicleTypeModel()
            ->removeVehicleType($idVehicleType);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idVehicle
     * @return VehicleTransfer
     */
    public function getVehicleById(int $idVehicle): VehicleTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleModel()
            ->getVehicleById($idVehicle);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return array
     */
    public function getVehiclesByFkBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createVehicleModel()
            ->getVehiclesByFkBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param VehicleTransfer $vehicleTransfer
     * @return VehicleTransfer
     */
    public function addVehicle(VehicleTransfer $vehicleTransfer): VehicleTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleModel()
            ->save($vehicleTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param VehicleTransfer $vehicleTransfer
     * @return VehicleTransfer
     */
    public function updateVehicle(VehicleTransfer $vehicleTransfer): VehicleTransfer
    {
        return $this
            ->getFactory()
            ->createVehicleModel()
            ->save($vehicleTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param $idVehicle
     * @return VehicleTransfer
     */
    public function removeVehicle($idVehicle): VehicleTransfer
    {
        return $this->getFactory()
            ->createVehicleModel()
            ->removeVehicle($idVehicle);
    }

    /**
     * {@inheritdoc}
     *
     * @return DrivingLicenceTransfer[]
     */
    public function getDrivingLicences(): array
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->getDrivingLicences();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDrivingLicence
     * @return DrivingLicenceTransfer
     */
    public function getDrivingLicenceById(int $idDrivingLicence): DrivingLicenceTransfer
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->getDrivingLicenceById($idDrivingLicence);
    }

    /**
     * {@inheritdoc}
     *
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     */
    public function addDrivingLicence(DrivingLicenceTransfer $drivingLicenceTransfer): DrivingLicenceTransfer
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->save($drivingLicenceTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     */
    public function updateDrivingLicence(DrivingLicenceTransfer $drivingLicenceTransfer): DrivingLicenceTransfer
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->save($drivingLicenceTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDrivingLicence
     * @return void
     */
    public function removeDrivingLicenceById(int $idDrivingLicence)
    {
        $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->removeById($idDrivingLicence);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return bool
     */
    public function drivingLicenceWithCodeExists(string $code): bool
    {
        return $this
            ->getFactory()
            ->createDrivingLicenceModel()
            ->drivingLicenceWithCodeExists($code);
    }

    /**
     * {@inheritdoc}
     *
     * @return TourExportMapper
     */
    public function getTourExportMapper(): TourExportMapper
    {
        return $this
            ->getFactory()
            ->createTourExportMapper();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function generateAllConcreteToursForExistingConcreteTimeSlotsInFuture(): void
    {
        $this
            ->getFactory()
            ->createConcreteTourModel()
            ->generateAllConcreteToursForExistingConcreteTimeSlotsInFuture();
    }

    /**
     * {@inheritdoc}
     *
     * @return ConcreteTourExportTransfer[]
     */
    public function getConcreteToursForEdiExport(): array
    {
        return $this
            ->getFactory()
            ->createConcreteTourExportModel()
            ->getConcreteToursForEdiExport();
    }

    /**
     * {@inheritdoc}
     *
     * @return TourExportParser
     */
    public function getTourExportParser(): TourExportParser
    {
        return $this
            ->getFactory()
            ->createTourExportParser();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTourExport
     */
    public function removeConcreteTourExportById(int $idConcreteTourExport)
    {
        $this
            ->getFactory()
            ->createConcreteTourExportModel()
            ->removeById($idConcreteTourExport);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function saveConcreteToursToExport(): int
    {
        return $this
            ->getFactory()
            ->createConcreteTourExportModel()
            ->saveConcreteToursToExport();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForExport(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->flagConcreteTourForExport($idConcreteTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForBeingExported(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->flagConcreteTourForBeingExported($idConcreteTour);
    }

    /**
     * {@inheritdoc}
     *
     * @return EdifactReferenceGenerator
     */
    public function getEdifactReferenceGenerator(): EdifactReferenceGenerator
    {
        return $this
            ->getFactory()
            ->createEdifactReferenceGenerator();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTour
     * @param string $endpointUrl
     * @param int $timeout
     * @param bool $isGraphmastersTour
     *
     * @return int
     */
    public function ediExportTourById(
        int $idTour,
        string $endpointUrl,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int {
        return $this
            ->getFactory()
            ->createEdiExportManager()
            ->ediExportTourById($idTour, $endpointUrl, $timeout, $isGraphmastersTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @param string $endPoint
     * @param int $timeout
     * @return int
     */
    public function ediExportDepositById(
        int $idTour,
        string $endPoint,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int {
        return $this
            ->getFactory()
            ->createEdiExportManager()
            ->ediExportDepositById($idTour, $endPoint, $timeout, $isGraphmastersTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTour
     * @param bool $isGraphmastersTour
     * @return EdiDepositExportUtil
     * @throws ContainerKeyNotFoundException
     */
    public function getEdiDepositExportUtil(int $idTour, bool $isGraphmastersTour = false): EdiDepositExportUtil
    {
        return $this
            ->getFactory()
            ->createEdiDepositExportUtil($idTour, $isGraphmastersTour);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getConcreteTourOrderableStatus(): string
    {
        return $this
            ->getFactory()
            ->getConfig()
            ->getConcreteTourOrderableStatus();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idAbstractTour
     * @return bool
     */
    public function hasPreparationStartingSameTimeForAllTimeSlots(int $idAbstractTour): bool
    {
        return $this
            ->getFactory()
            ->createAbstractTourModel()
            ->hasPreparationStartingSameTimeForAllTimeSlots($idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForCommissioned(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->flagConcreteTourForCommissioned($idConcreteTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $tourReference
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourByTourReference(string $tourReference): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->getConcreteTourByTourReference($tourReference);
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkConcreteTimeSlotHasConcreteTour(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        return $this
            ->getFactory()
            ->createTimeSlotConditionChecker()
            ->checkConcreteTimeSlotHasConcreteTour($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param DriverTransfer $driverTransfer
     * @return array
     */
    public function getOrdersWithToursByDriver(DriverTransfer $driverTransfer): array
    {
        return $this
            ->getFactory()
            ->createTourOrderModel()
            ->getOrdersWithToursByDriver($driverTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTourExport
     */
    public function setConcreteTourExportInProgress(int $idConcreteTourExport): void
    {
        $this
            ->getFactory()
            ->createConcreteTourExportModel()
            ->setExportInProgress($idConcreteTourExport);
    }

    /**
     * {@inheritDoc}
     *
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function updateConcreteTour(ConcreteTourTransfer $concreteTourTransfer): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->save($concreteTourTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     * @return bool
     */
    public function itemStateUpdate(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        return $this
            ->getFactory()
            ->createStateMachineTourItemSaver()
            ->itemStateUpdate($stateMachineItemTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $stateIds
     * @return StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        return $this
            ->getFactory()
            ->createStateMachineTourItemReader()
            ->getStateMachineItemsByStateIds($stateIds);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @param array $excludedOrderIds
     * @return bool
     */
    public function hasConcreteTourOpenOrdersWithExcludedIds(int $idConcreteTour, array $excludedOrderIds): bool
    {
        return $this
            ->getFactory()
            ->createTourOrderModel()
            ->hasConcreteTourOpenOrdersWithExcludedIds($idConcreteTour, $excludedOrderIds);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function isConcreteTourCommissioned(int $idConcreteTour): bool
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->isConcreteTourCommissioned($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @param string $status
     * @return void
     */
    public function updateConcreteTourDepositEdiStatus(int $idConcreteTour, string $status): void
    {
        $this
            ->getFactory()
            ->createConcreteTourModel()
            ->updateConcreteTourDepositEdiStatus(
                $idConcreteTour,
                $status
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function triggerManualDepositEdiExport(int $idConcreteTour): bool
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->triggerManualDepositEdiExport(
                $idConcreteTour
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateOrderByTourId(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createTourOrderHydrator()
            ->hydrateOrderByTourId($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourExportTransfer
     */
    public function getConcreteTourExportForIdConcreteTour(int $idConcreteTour): ConcreteTourExportTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourExportModel()
            ->getConcreteTourExportByIdConcreteTour($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array
    {
        return $this
            ->getFactory()
            ->createTourOrderModel()
            ->getOrdersByIdConcreteTour($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return GraphhopperTourTransfer
     */
    public function mapTourToGraphhopper(int $idConcreteTour) : GraphhopperTourTransfer
    {
        return $this
            ->getFactory()
            ->createTourToGraphhopperMapper()
            ->mapTourToGraphhopper($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param DriverTransfer $driverTransfer
     *
     * @return array
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array
    {
        return $this
            ->getFactory()
            ->createTourOrderModel()
            ->getToursForDriver($driverTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param DstConcreteTour $concreteTourEntity
     * @return ConcreteTourTransfer
     */
    public function convertConcreteTourEntityToTransfer(DstConcreteTour $concreteTourEntity): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->entityToTransfer($concreteTourEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $fkBranch
     * @return array
     * @throws AmbiguousComparisonException|PropelException
     */
    public function getConcreteTourDatesByFkBranch(int $fkBranch): array
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->getConcreteTourDatesByFkBranch($fkBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     * @throws ConcreteTourNotExistsException
     */
    public function flagConcreteTourForForcedEmptyExport(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->flagConcreteTourForForcedEmptyExport($idConcreteTour);
    }

    /**
     * @param array $concreteTourEntityArray
     * @return ConcreteTourTransfer
     */
    public function convertConcreteTourEntityArrayToTransferForIndex(
        array $concreteTourEntityArray
    ): ConcreteTourTransfer {
        return $this
            ->getFactory()
            ->createConcreteTourModel()
            ->entityArrayToTransferForIndex($concreteTourEntityArray);
    }

    /**
     * @return TourDriverappMapperInterface
     */
    public function getTourDriverAppMapper(): TourDriverappMapperInterface
    {
        return $this
            ->getFactory()
            ->createTourDriverAppMapper();
    }

    /**
     * @return TourConfig
     */
    public function getConfig(): TourConfig
    {
        return $this->getFactory()->getConfig();
    }
}
