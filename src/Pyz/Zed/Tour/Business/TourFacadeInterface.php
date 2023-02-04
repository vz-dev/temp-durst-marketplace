<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 12:42
 */

namespace Pyz\Zed\Tour\Business;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourExportTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverAppTourTransfer;
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
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Business\Parser\TourExportParser;
use Pyz\Zed\Tour\Business\Util\EdiDepositExportUtil;
use Pyz\Zed\Tour\TourConfig;

interface TourFacadeInterface
{
    /**
     * Adds the given abstract tour transfer object to the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function addAbstractTour(AbstractTourTransfer $abstractTourTransfer) : AbstractTourTransfer;

    /**
     * Updates the given abstract tour transfer object in the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function updateAbstractTour(AbstractTourTransfer $abstractTourTransfer) : AbstractTourTransfer;

    /**
     * Returns an array of transfer objects representing all abstract tours in the database
     * with status not deleted and referring to the given branch id.
     *
     * @param int $idBranch
     * @return AbstractTourTransfer[]
     */
    public function getAbstractToursByFkBranch(int $idBranch) : array;

    /**
     * Specification:
     *  - Sets the status of the abstract tour with the given id to "deleted"
     *  - Triggers the "delete" event for all related concrete tours
     *    * where all related concrete time slots don't have orders
     *    * that don't have related concrete time slots
     *  - If no abstract tour with the id exists an exception is being thrown
     *  - A transfer object with the updated data will be returned
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function deleteAbstractTour(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * Returns a fully hydrated transfer object matching the abstract Tour in the data base with the given id.
     *
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function getAbstractTourById(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * Returns a fully hydrated transfer object matching the concrete Tour in the data base with the given id.
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourById(int $idConcreteTour) : ConcreteTourTransfer;

    /**
     * Sets the status of an abstractTour given by it's id in the database to active.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param int $idAbstractTour
     * @return $AbstractTourTransfer
     */
    public function activateAbstractTour(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * Sets the status of an abstractTour given by it's id in the database to deativated.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param int $idAbstractTour
     * @return $AbstractTourTransfer
     */
    public function deactivateAbstractTour(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * Returns a fully hydrated transfer object matching the vehicle type in the data base with the given id.
     *
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function getVehicleTypeById(int $idVehicleType) : VehicleTypeTransfer;

    /**
     * Adds the given vehicle type transfer object to the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     */
    public function addVehicleType(VehicleTypeTransfer $vehicleTypeTransfer) : VehicleTypeTransfer;

    /**
     * Updates the given vehicle type transfer object in the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     */
    public function updateVehicleType(VehicleTypeTransfer $vehicleTypeTransfer) : VehicleTypeTransfer;

    /**
     * Returns an array of transfer objects representing all vehicle types in the database
     * with status active and referring to the given branch id.
     *
     * @param int $idBranch
     * @return VehicleTypeTransfer[]
     */
    public function getVehicleTypesByFkBranch(int $idBranch) : array;

    /**
     * Sets the status of a vehicle type given by it's id in the database to deleted.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function removeVehicleType(int $idVehicleType) : VehicleTypeTransfer;

    /**
     * Returns an array of transfer objects representing all vehicles in the database
     * referring to the given fkBranch.
     *
     * @param int $idBranch
     * @return array
     */
    public function getVehiclesByFkBranch(int $idBranch) : array;

    /**
     * Returns an array of transfer objects representing all driving licences in the database.
     *
     * @return DrivingLicenceTransfer[]
     */
    public function getDrivingLicences() : array;

    /**
     * Returns a fully hydrated transfer object matching the driving licence in the data base with the given id.
     *
     * @param int $idDrivingLicence
     * @return DrivingLicenceTransfer
     */
    public function getDrivingLicenceById(int $idDrivingLicence) : DrivingLicenceTransfer;

    /**
     * Returns a fully hydrated transfer object matching the vehicle in the data base with the given id.
     *
     * @param int $idVehicle
     * @return VehicleTransfer
     * @throws Exception\VehicleNotExistsException
     */
    public function getVehicleById(int $idVehicle) : VehicleTransfer;

    /**
     * Adds the given vehicle transfer to the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param VehicleTransfer $vehicleTransfer
     * @return VehicleTransfer
     */
    public function addVehicle(VehicleTransfer $vehicleTransfer) : VehicleTransfer;

    /**
     * Updates the given vehicle transfer in the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param VehicleTransfer $vehicleTransfer
     * @return VehicleTransfer
     */
    public function updateVehicle(VehicleTransfer $vehicleTransfer) : VehicleTransfer;

    /**
     * Sets the status of a vehicle given by its id in the database to deleted.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param $idVehicle
     * @return VehicleTransfer
     */
    public function removeVehicle($idVehicle) : VehicleTransfer;

    /**
     * Adds the given driving licence transfer to the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     * @throws Exception\DrivingLicenceExistsException
     */
    public function addDrivingLicence(DrivingLicenceTransfer $drivingLicenceTransfer) : DrivingLicenceTransfer;

    /**
     * Updates the given driving licence transfer in the database.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     * @throws Exception\DrivingLicenceExistsException
     */
    public function updateDrivingLicence(DrivingLicenceTransfer $drivingLicenceTransfer) : DrivingLicenceTransfer;

    /**
     * Removes the driving licence given by it's id from the database.
     *
     * @param int $idDrivingLicence
     * @return void
     */
    public function removeDrivingLicenceById(int $idDrivingLicence);

    /**
     * Checks if a driving licence with the given code already exists in the data base.
     * Returns true if the given code already exists.
     *
     * @param string $code
     * @return bool
     */
    public function drivingLicenceWithCodeExists(string $code) : bool;

    /**
     * For a concrete TimeSlot given by a corresponding transfer object
     * a concrete tour is generated if possible and stored in the database
     * A fully hydrated transfer object corresponding with the newly created
     * or already existing concrete tour will be returned.
     * If no concrete tour has been created an empty concrete tour object transfer
     * will be returned.
     *
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTourTransfer
     */
    public function createConcreteTourForConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTourTransfer;

    /**
     * Creates an instance of a mapper for exporting tours (EDI)
     *
     * @return TourExportMapper
     */
    public function getTourExportMapper() : TourExportMapper;

    /**
     * Just persists the comment of a given concrete tour transfer object
     * in the database and returns a fully hydrated transfer object of the concrete tour includind the new comment.
     *
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function commentConcreteTour(ConcreteTourTransfer $concreteTourTransfer) : ConcreteTourTransfer;

    /**
     * Generates concrete Tours for all existing concrete time slots in future
     * that are created from abstract time slots being planned in an abstract tour.
     *
     * @return void
     */
    public function generateAllConcreteToursForExistingConcreteTimeSlotsInFuture() : void;

    /**
     * Get a list of concrete tours flagged for EDI export
     *
     * @return ConcreteTourExportTransfer[]
     */
    public function getConcreteToursForEdiExport(): array;

    /**
     * Get a parser instance for EDIFACT D96a parsing
     *
     * @return TourExportParser
     */
    public function getTourExportParser(): TourExportParser;

    /**
     * Delete a concrete tour export by its Id
     *
     * @param int $idConcreteTourExport
     * @return void
     */
    public function removeConcreteTourExportById(int $idConcreteTourExport);

    /**
     * Get a list of concrete tours which needs to be exported
     * and inserts these into the export table
     *
     * Returns the number of created concrete tour export entries
     *
     * @return int
     */
    public function saveConcreteToursToExport(): int;

    /**
     * Set flag for a concrete tour that it can be exported now
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForExport(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Set flag for a concrete tour that it has been exported
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForBeingExported(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Create a sequence reference generator for generating
     * message and data reference numbers
     * used in the Edifact D96a export.
     *
     * @return EdifactReferenceGenerator
     */
    public function getEdifactReferenceGenerator(): EdifactReferenceGenerator;

    /**
     * Create an EDI export manager
     * and exports a concrete tour into EDI format
     *
     * @param int $idTour
     * @param string $endpointUrl
     * @param int $timeout
     * @param bool $isGraphmastersTour
     * @return int
     */
    public function ediExportTourById(
        int $idTour,
        string $endpointUrl,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int;

    /**
     * Create an EDI export manager
     * and exports the deposit of a concrete tour into EDI format
     *
     * @param int $idTour
     * @param string $endPoint
     * @param int $timeout
     * @param bool $isGraphmastersTour
     * @return int
     */
    public function ediExportDepositById(
        int $idTour,
        string $endPoint,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int;

    /**
     * Create an export utility for sending deposit data with EDI
     *
     * @param int $idTour
     * @return EdiDepositExportUtil
     */
    public function getEdiDepositExportUtil(int $idTour): EdiDepositExportUtil;

    /**
     * Returns the Display name for the status 'orderable' from the TourConfig
     *
     * @return string
     */
    public function getConcreteTourOrderableStatus(): string;

    /**
     * Returns true if all abstract time slots included in the given abstract tour
     * have preparation time starting at the same time.
     * Returns false, if not.
     *
     * @param int $idAbstractTour
     * @return bool
     */
    public function hasPreparationStartingSameTimeForAllTimeSlots(int $idAbstractTour): bool;

    /**
     * Set flag for concrete tour after the EDI export to verify
     * that the tour has been commissioned
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForCommissioned(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Return a concrete tour by the given tour reference
     *
     * @param string $tourReference
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourByTourReference(string $tourReference): ConcreteTourTransfer;

    /**
     * Specification:
     *  - first, checks if the order provided by the quote transfer is ordered at a branch from a wholesale merchant
     *  - if not, true will be returned and no further checks apply
     *  - otherwise, checks whether the concrete time slot selected in the quote transfer is linked to a concrete
     *    tour
     *  - if so true will be returned
     *  - otherwise an error will be added to the checkout response transfer and false will be returned
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkConcreteTimeSlotHasConcreteTour(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;

    /**
     * Return a list of all concrete tours for a given driver
     *
     * @param DriverTransfer $driverTransfer
     * @return OrderTransfer[]
     */
    public function getOrdersWithToursByDriver(DriverTransfer $driverTransfer): array;

    /**
     * Specification:
     *  - eager loads all tours that:
     *       - don't have a driver
     *       - are matched to the given driver
     *       - should be delivered today
     *       - should be delivered in the past
     *  - returns an array with the following information ordered by start time of time slot
     *       - tour
     *       - orders
     *       - order items
     *       - time slot
     *       - shipping address
     *       - billing address
     *       - oms state
     *       - oms process
     *       - payment method
     *       - order comment
     *
     * @param DriverTransfer $driverTransfer
     * @return array|DriverAppTourTransfer[]
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array;

    /**
     * Set an EDI export of a concrete tour (by its id) to "in progress"
     * This should avoid multiple exports for the same tour (DST-3124)
     *
     * @param int $idConcreteTourExport
     */
    public function setConcreteTourExportInProgress(int $idConcreteTourExport): void;

    /**
     * Update and save the given concrete tour transfer
     *
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function updateConcreteTour(ConcreteTourTransfer $concreteTourTransfer): ConcreteTourTransfer;

    /**
     * Update state of given item
     *
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     * @return bool
     */
    public function itemStateUpdate(StateMachineItemTransfer $stateMachineItemTransfer): bool;

    /**
     * @param int[] $stateIds
     * @return StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array;

    /**
     * Specification:
     *  - Loads all order that are connected to the tour defined by the given id
     *  - But where the ids not match one of the given excluded order ids
     *  - Checks if these order have at least one in the state defined in configured accepted state
     *
     * @see \Pyz\Shared\Oms\OmsConstants::OMS_RETAIL_ACCEPTED_STATE
     * @see \Pyz\Shared\Oms\OmsConstants::OMS_WHOLESALE_ACCEPTED_STATE
     *
     * @param int $idConcreteTour
     * @package int[] $excludedOrderIds
     *
     * @return bool
     */
    public function hasConcreteTourOpenOrdersWithExcludedIds(int $idConcreteTour, array $excludedOrderIds): bool;

    /**
     * Check, if a concrete tour with the given id is flag as commissioned
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function isConcreteTourCommissioned(int $idConcreteTour): bool;

    /**
     * Set status on concrete tour concerning deposit EDI export
     *
     * @param int $idConcreteTour
     * @param string $status
     * @return void
     */
    public function updateConcreteTourDepositEdiStatus(int $idConcreteTour, string $status): void;

    /**
     * Trigger the correct event from state machine to export the deposit EDI
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function triggerManualDepositEdiExport(int $idConcreteTour): bool;

    /**
     * Specification:
     *  - stores the id of the tour the order is belonging to in the order transfer
     *  - stores the id of the tour item state
     *  - if the order does not belong to a tour null is stored for tour id and tour item state id
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateOrderByTourId(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - returns export transfer for concrete tour matching the given id
     *  - if no export can be found an exception is being thrown
     *
     * @param int $idConcreteTour
     * @return ConcreteTourExportTransfer
     */
    public function getConcreteTourExportForIdConcreteTour(int $idConcreteTour): ConcreteTourExportTransfer;

    /**
     * Return a list of all orders with a valid state (i.e. ready for delivery) for a given concrete tour by its id
     *
     * @param int $idConcreteTour
     * @return OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array;

    /**
     * Creates and returns GraphhopperTourtransfer for all orders with a valid state (i.e. ready for delivery) that belong to the
     * tour with the corresponding tour id
     *
     * Exceptions:
     *  - TourToGraphhopperMapperNoOrdersException: Thrown when the tour has no orders
     *  - TourToGraphhopperMapperNoBranchCoordinatesException: Thrown when the branch has no warehouse coordinates
     *
     * @param int $idConcreteTour
     * @return GraphhopperTourTransfer
     */
    public function mapTourToGraphhopper(int $idConcreteTour) : GraphhopperTourTransfer;

    /**
     * Converts a concrete tour entity to a transfer object
     *
     * @param DstConcreteTour $concreteTourEntity
     * @return ConcreteTourTransfer
     */
    public function convertConcreteTourEntityToTransfer(DstConcreteTour $concreteTourEntity): ConcreteTourTransfer;

    /**
     * Returns a list of the concrete tour dates for a given branch
     *
     * @param int $fkBranch
     * @return array
     */
    public function getConcreteTourDatesByFkBranch(int $fkBranch): array;

    /**
     * Returns an array of transfer objects representing all vehicle categories in the database
     * with status active.
     *
     * @return VehicleCategoryTransfer[]
     */
    public function getActiveVehicleCategories() : array;

    /**
     * Specification:
     *  - sets flag for a concrete tour that it must be exported even if it is empty
     *  - only applies if the tour is also flagged exportable (see above)
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     * @throws ConcreteTourNotExistsException
     */
    public function flagConcreteTourForForcedEmptyExport(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Converts a concrete tour entity array to a transfer object hydrated for index only
     *
     * @param array $concreteTourEntityArray
     * @return ConcreteTourTransfer
     */
    public function convertConcreteTourEntityArrayToTransferForIndex(
        array $concreteTourEntityArray
    ): ConcreteTourTransfer;

    /**
     * Returns a mapper that maps tour entities to Driver App tour transfers
     *
     * @return TourDriverappMapperInterface
     */
    public function getTourDriverAppMapper(): TourDriverappMapperInterface;

    /**
     * Returns tour configuration
     *
     * @return TourConfig
     */
    public function getConfig(): TourConfig;
}
