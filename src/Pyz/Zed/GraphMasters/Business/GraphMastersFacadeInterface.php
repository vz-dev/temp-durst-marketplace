<?php

namespace Pyz\Zed\GraphMasters\Business;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

interface GraphMastersFacadeInterface
{
    /**
     * Function to import Orders into Graphmasters based on the provided QuoteTransfer
     * includes check to see if the branch uses GraphMasters or not.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrder
     * @return void
     */
    public function importOrder(QuoteTransfer $quoteTransfer, int $idSalesOrder): void;

    /**
     * saves graphmaster setting using the settings transfer as a basis
     *
     * @param GraphMastersSettingsTransfer $transfer
     *
     * @return void
     */
    public function saveSetting(GraphMastersSettingsTransfer $transfer): void;

    /**
     * remove graphmaster setting based on settings id
     *
     * @param int $idSettings
     *
     * @return void
     */
    public function removeSettings(int $idSettings): void;

    /**
     * Returns bool checks if there is a graphmasters settings entry, for the branch with the given id and
     * checks if is_active
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseGraphmasters(int $idBranch): bool;

    /**
     * Get Graphmasters settings with the given ID, optionally with related objects (opening and commissioning times)
     *
     * @param int $idSettings
     * @param bool $withRelatedObjects
     * @return GraphMastersSettingsTransfer
     * @throws PropelException
     */
    public function getSettingsById(int $idSettings, bool $withRelatedObjects = false): GraphMastersSettingsTransfer;

    /**
     * Get Graphmasters Settings for Branch with the given id
     *
     * @param int $idBranch
     * @return GraphMastersSettingsTransfer
     */
    public function getSettingsByIdBranch(int $idBranch): GraphMastersSettingsTransfer;

    /**
     * Returns delivery area category with the given ID, optionally limited to the current branch
     *
     * @param int $idCategory
     * @param bool $currentBranchOnly
     * @return GraphMastersDeliveryAreaCategoryTransfer
     */
    public function getDeliveryAreaCategoryById(int $idCategory, bool $currentBranchOnly = false): GraphMastersDeliveryAreaCategoryTransfer;

    public function saveDeliveryAreaCategory(GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer, int $fkBranch = null): GraphMastersDeliveryAreaCategoryTransfer;

    public function getCategoryDeliveryAreasByCategoryId(int $catId);

    public function evaluateTimeSlot(AppApiRequestTransfer $appApiRequestTransfer) : AppApiResponseTransfer;

    public function generateTimeSlots() : void;

    /**
     * Returns all delivery area categories for the branch with the given ID
     *
     * @param int $idBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreaCategoriesByIdBranch(int $idBranch): array;

    /**
     * Removes delivery area category with the given ID
     *
     * @param int $idDeliveryAreaCategory
     * @throws PropelException
     */
    public function removeDeliveryAreaCategory(int $idDeliveryAreaCategory): void;

    public function buildTimeslotPageMap(PageMapBuilderInterface $pageMapBuilder, array $timeslotData, LocaleTransfer $locale): PageMapTransfer;

    /**
     * Imports tours from Graphmasters using their API
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function importTours(): void;

    /**
     * Returns Graphmasters tour with the given ID
     *
     * @param int $idTour
     * @return GraphMastersTourTransfer
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourById(int $idTour): GraphMastersTourTransfer;

    /**
     * Converts a Graphmasters tour entity to a transfer object
     *
     * @param DstGraphmastersTour $tourEntity
     * @return GraphMastersTourTransfer
     */
    public function convertTourEntityToTransfer(DstGraphmastersTour $tourEntity): GraphMastersTourTransfer;

    /**
     * Fixes the orders of the Graphmasters tour with the given internal ID.
     * No orders can be assigned to or unassigned from this tour afterwards.
     *
     * @param int $idTour
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function fixTourById(int $idTour): void;


    /**
     * Fixes the all open tours that have reached there cutoff time and have not yet
     * had the status edi goods exported set to true
     *
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function fixOpenToursCutoffReached() : void;

    /**
     * Persists the comment of a given Graphmasters tour transfer object in the
     * database and returns a fully hydrated transfer object of the Graphmasters
     * tour includind the new comment
     *
     * @param GraphMastersTourTransfer $graphmastersTourTransfer
     */
    public function commentTour(GraphMastersTourTransfer $graphmastersTourTransfer): void;

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
     * @throws ContainerKeyNotFoundException
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array;

    /**
     * Sets the status of the Graphmasters order with the given reference to
     * "finished" and communicates it to the API
     *
     * @param string $orderReference
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderFinishedByReference(string $orderReference): void;

    /**
     * Sets the status of the Graphmasters order with the given reference to
     * "cancelled" and communicates it to the API
     *
     * @param string $orderReference
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderCancelledByReference(string $orderReference): void;

    /**
     * Saves Graphmasters orders using the given transfer
     *
     * @param GraphMastersOrderTransfer $transfer
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function saveGraphmastersOrder(GraphMastersOrderTransfer $transfer): void;

    /**
     * Returns if Graphmasters order with the given reference is marked cancelled
     *
     * @param string $orderReference
     * @return bool
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function isOrderMarkedCancelled(string $orderReference): bool;

    /**
     * @param string $tourReference
     * @return GraphMastersTourTransfer
     * @throws ContainerKeyNotFoundException
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourByReference(string $tourReference): GraphMastersTourTransfer;

    /**
     * Returns Graphmasters tour with the given reference
     *
     * @param string $orderReference
     * @return GraphMastersOrderTransfer
     * @throws EntityNotFoundException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getOrderByReference(string $orderReference): GraphMastersOrderTransfer;

    /**
     * Specification:
     *   - returns bool
     *   - checks if there is a active category which contains the given zip
     *   - checks for active branch with given branchcode
     *
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliversByZipAndBranchCode(string $zipCode, string $branchCode) : bool;
}
