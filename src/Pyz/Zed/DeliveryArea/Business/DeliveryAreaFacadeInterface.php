<?php

namespace Pyz\Zed\DeliveryArea\Business;

use DateTime;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Generated\Shared\Transfer\DiscountableItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

interface DeliveryAreaFacadeInterface
{
    /**
     * Adds a given delivery area transfer object to the database.
     *
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     */
    public function createDeliveryArea(DeliveryAreaTransfer $deliveryAreaTransfer);

    /**
     * Checks if delivery areas are already imported
     *
     * @return bool returns true if there is at least one delivery area in the database
     */
    public function deliveryAreasAreImported();

    /**
     * Adds a new delivery area to the database with the given fields and
     * returns a fully hydrated transfer object of the added data set.
     *
     * @param string $name
     * @param string $city
     * @param int $zip
     *
     * @return DeliveryAreaTransfer
     */
    public function addDeliveryArea($name, $city, $zip);

    /**
     * Updates the delivery area in the database so it matches the given transfer object.
     * The updated, fully hydrated transfer object will be returned.
     *
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     *
     * @return DeliveryAreaTransfer
     */
    public function updateDeliveryArea(DeliveryAreaTransfer $deliveryAreaTransfer);

    /**
     * Removes the delivery area matching the given id from the database.
     *
     * @param int $idDeliveryArea id of the delivery area that shall be deleted. If no data set with this
     * id is found an exception is thrown
     *
     * @return void
     */
    public function removeDeliveryArea($idDeliveryArea);

    /**
     * Adds a given time slot transfer object to the database.
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     */
    public function createTimeSlot(TimeSlotTransfer $timeSlotTransfer);

    /**
     * @return bool returns true if there is at least one time slot in the database
     */
    public function timeSlotsAreImported();

    /**
     * Updates the time slot in the database, so it matches the given transfer object.
     * The updated transfer object will be returned.
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     *
     * @return TimeSlotTransfer
     */
    public function updateTimeSlot(TimeSlotTransfer $timeSlotTransfer);

    /**
     * Adds a time slot to the database matching the given transfer object.
     * The fully hydrated transfer object will be returned.
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     *
     * @return TimeSlotTransfer
     */
    public function addTimeSlot(TimeSlotTransfer $timeSlotTransfer);

    /**
     * Removes the time slot matching the given id from the database.
     *
     * @param int $idTimeSlot
     *
     * @return void
     */
    public function removeTimeSlot($idTimeSlot);

    /**
     * Returns true if there is a delivery area in the database with the given id
     *
     * @param int $idDeliveryArea
     *
     * @return bool
     */
    public function hasDeliveryArea($idDeliveryArea);

    /**
     * Returns an array of transfer objects representing all delivery areas in the database.
     *
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreas();

    /**
     * Returns a fully hydrated transfer object representing the delivery area matching
     * the given id. If no delivery area with the given id exists an exception will be thrown!
     *
     * @param int $idDeliveryArea
     *
     * @return DeliveryAreaTransfer
     */
    public function getDeliveryAreaById($idDeliveryArea);

    /**
     * Returns an array of fully hydrated transfer objects representing all time slots
     * of a given branch defined by its id.
     *
     * @param int $idBranch
     *
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranch($idBranch);

    /**
     * Returns an array of fully hydrated transfer objects representing all time slots
     * of a given branch defined by its id that are valid on the given weekday.
     *
     * @param int $idBranch
     * @param string $weekday
     *
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchAndWeekday(int $idBranch, string $weekday): array;

    /**
     * Returns a fully hydrates transfer object representing the time slot
     * matching the given id
     *
     * @param int $idTimeSlot
     *
     * @return TimeSlotTransfer
     */
    public function getTimeSlotById($idTimeSlot);

    /**
     * Returns an array of fully hydrated transfer objects representing all delivery areas
     * a given branch defined by its id delivers to.
     *
     * @param int $idBranch
     *
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasByIdBranch($idBranch);

    /**
     * Returns an array of fully hydrated transfer objects representing the time slots
     * that matches a given branch defined by its id has for a given delivery area
     * also defined by its id.
     *
     * @param int $idBranch
     * @param int $idDeliveryArea
     *
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotByIdBranchAndIdDeliveryArea($idBranch, $idDeliveryArea);

    /**
     * Returns a fully hydrated delivery area transfer matching the given zip code
     * and name.
     *
     * @param int $zip
     * @param string $name
     *
     * @return DeliveryAreaTransfer
     *@throws \Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException if no delivery area with the given
     * zip code and name exists in the database
     *
     */
    public function getDeliveryAreaByZipAndName($zip, $name);

    /**
     * Returns an array of transfer object where the time slot relationship is not
     * hydrated, this is useful for ajax auto completion where only the zip codes
     * and city names are required.
     *
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasWithoutTimeSlots();

    /**
     * Adds a concrete time slot to the database containing the data provided by the
     * given transfer object. A fully hydrated transfer object containing the id will be returned.
     *
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function createConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer);

    /**
     * returns a fully hydrated concrete time slot transfer matching
     * the given id
     *
     * @param $idConcreteTimeSlot
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotById(int $idConcreteTimeSlot) : ConcreteTimeSlotTransfer;

    /**
     * returns a fully hydrated concrete time slot transfer matching
     * the given id
     *
     * @param int $idConcreteTimeSlot
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotByIdIgnoreActive(int $idConcreteTimeSlot) : ConcreteTimeSlotTransfer;

    /**
     * Checks if there is already a concrete time slot matching the given data. If so the corresponding transfer
     * object will be returned, otherwise a new entity will be persisted and the corresponding transfer will be returned.
     *
     * @param DateTime|int $start
     * @param DateTime $end
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotForBranchByStartAndEnd(int $idBranch, DateTime $start, DateTime $end): ConcreteTimeSlotTransfer;

    /**
     * Returns a fully hydrated transfer object representing the delivery area matching
     * the given zip code
     *
     * @param string $zipCode
     *
     * @return DeliveryAreaTransfer
     *@throws Exception\DeliveryAreaNotFoundException
     *
     */
    public function getDeliveryAreaByZipCode(string $zipCode): DeliveryAreaTransfer;

    /**
     * Returns a fully hydrated transfer object representing the delivery area matching
     * the given zip or branch code
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return DeliveryAreaTransfer
     */
    public function getDeliveryAreaByZipOrBranchCode(
        string $zipCode,
        string $branchCode
    ): DeliveryAreaTransfer;

    /** Returns a array of ConcreteTimeSlots, based on branch ids and zip code the results are limited by both a overall total,
     * as well as a limit of concrete timeslots per abstract timeslot
     *
     * @param array $branchIds
     * @param string $zipCode
     * @param int $maxSlots
     * @param int $itemsPerSlot
     *
     * @return ConcreteTimeSlotTransfer[]
     */
    public function getConcreteTimeSlotsForBranchesAndZipCode(array $branchIds, string $zipCode, int $maxSlots, int $itemsPerSlot): array;

    /**
     * Hydrates the cart change transfer with the delivery cost for the time slot that is set in the cart.
     *
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartChangeTransfer
     */
    public function expandItemsByDeliveryCost(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;

    /**
     * Calculates the total delivery cost for the given time slot
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateDeliveryCostTotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Calculates the amount that is missing to satisfy the min value requirements of the branch
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateMissingMinValueTotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Calculates the amount that is missing to satisfy the min units required by the time slot
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateMissingMinUnits(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Hydrates the cart change transfer with the min value for the selected concrete time slot, if
     * this is would be the first order for the time slot min_value_first will be chosen, min_value_following otherwise.
     *
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartChangeTransfer
     */
    public function expandItemsByMinValue(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;

    /**
     * Hydrate the cart change transfer with the min units for the selected concrete time slot
     *
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartChangeTransfer
     */
    public function expandItemsByMinUnits(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;

    /**
     * Checks whether all assertions for the concrete time slot defined in the quote transfer are met.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkConcreteTimeSlotAssertionsForCheckout(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool;

    /**
     * Checks whether all assertions for the concrete time slot defined in the quote transfer are met.
     *
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartPreCheckResponseTransfer
     */
    public function validateConcreteTimeSlotAssertionsForCheckout(CartChangeTransfer $cartChangeTransfer): CartPreCheckResponseTransfer;

    /**
     * Checks whether the zip code of the delivery address matches the zip code of the time slot.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkZipCodesDeliveyAddressTimeSlotMatch(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool;

    /**
     * hydrates the corresponding ConcreteTimeSlotTransfer into the provided OrderTransfer
     * based on the FkConcreteTimeSlot
     *
     * @param OrderTransfer $orderTransfer
     *
     * @return OrderTransfer
     */
    public function hydrateConcreteTimeSlot(OrderTransfer $orderTransfer);

    /**
     * Sets the tax rate of the delivery cost expense in the expense object. For now this is
     * simply the default tax rate.
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     *@throws ContainerKeyNotFoundException
     *
     */
    public function calculateDeliveryCostTaxRate(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Specification:
     * - Adds delivery cost sales expense to sales order.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderDeliveryCost(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);

    /**
     * Build a page map for transferring Propel entity DeliveryArea to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $deliveryareaData
     * @param LocaleTransfer $locale
     *
     * @return PageMapTransfer
     */
    public function buildDeliveryAreaPageMap(PageMapBuilderInterface $pageMapBuilder, array $deliveryareaData, LocaleTransfer $locale): PageMapTransfer;

    /**
     * Build a page map for transferring Propel entity Timeslot to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $timeslotData
     * @param LocaleTransfer $locale
     *
     * @return PageMapTransfer
     */
    public function buildTimeslotPageMap(PageMapBuilderInterface $pageMapBuilder, array $timeslotData, LocaleTransfer $locale): PageMapTransfer;

    /**
     * This will create all future concrete time slots fot all active time slots of all active branches,
     * that are still within the limit configured in the config
     *
     * @see \Pyz\Shared\DeliveryArea\DeliveryAreaConstants::CONCRETE_TIME_SLOT_CREATION_LIMIT
     *
     * @return void
     */
    public function createFutureConcreteTimeSlots();

    /**
     * Creates a human readable string from two DateTime objects formatted like this:
     * "Freitag, 23.11.18 - 15:00 bis 16:00 Uhr"
     *
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return string
     */
    public function createFormattedTimeSlotString(
        DateTime $start,
        DateTime $end
    ): string;

    /**
     * Touches all concrete time slots that lay in the past so they will be removed from elasticsearch
     *
     * @return void
     */
    public function touchDeletePassedConcreteTimeSlots();

    /**
     * Persists only the id of the corresponding Concrete Tour of an concrete time slot given by a transfer object
     * and returns a fully hydrated transfer object of the modified concrete time slot entity.
     *
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function setFkConcreteTourInConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTimeSlotTransfer;

    /**
     * Retunrs fully hydrated transfer object of all already created concrete time slots in future
     * with no relation to a concrete tour yet.
     *
     * @return ConcreteTimeSlotTransfer[]
     */
    public function getConcreteTimeSlotsInFutureWithoutConcreteTour() : array;

    /**
     * PostSaveHook that touchs all concrete timeslots that are associated with a order including all concrete
     * timeslots that are part of the sme concrete tour
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     *
     * @return bool
     */
    public function runConcreteTimeSlotTouchPostSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): bool;

    /**
     * Specification:
     *  - receives all delivery area and time slot data for the given branch in chunks of
     *
     * @see \Pyz\Zed\DeliveryArea\DeliveryAreaConfig::EXPORT_CHUNK_SIZE
     *  - processes the data asynchronously into a csv file
     *  - sends the csv file to the provided email address
     *  - email will be converted to lowercase and email validated
     *
     * @param int $idBranch
     * @param array $emails
     * @param int $page
     * @param string|null $filename
     */
    public function createTimeSlotExportAndSendToEmailForBranch(int $idBranch, array $emails, int $page = 1, ?string $filename = null): void;

    /**
     * Specification:
     *  - imports (updates/deletes/creates) all time slots passed in the csv file provided in json
     *  - checks that time slots actually belongs to branch when updating or deleting otherwise throws exception
     *  - rollbacks should a error occur during import
     *  - sends success or fail mail to the branch email
     *
     * @param string $importJson
     * @return void
     */
    public function importTimeSlotsForBranchByCsv(string $importJson);

    /**
     * Delete a concrete timeslot identified by its ID and the branch
     * From database and (Elasticsearch) search storage
     *
     * @param int $idConcreteTimeSlot
     * @param int $idBranch
     * @return void
     */
    public function deleteConcreteTimeSlotByIdAndBranch(
        int $idConcreteTimeSlot,
        int $idBranch
    ): void;

    /**
     * Converts a delivery area entity to a transfer object
     *
     * @param SpyDeliveryArea $deliveryAreaEntity
     * @return DeliveryAreaTransfer
     */
    public function convertDeliveryAreaEntityToTransfer(SpyDeliveryArea $deliveryAreaEntity): DeliveryAreaTransfer;

    /**
     * Converts a concrete time slot entity to a transfer object
     *
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return ConcreteTimeSlotTransfer
     */
    public function convertConcreteTimeSlotEntityToTransfer(
        SpyConcreteTimeSlot $concreteTimeSlotEntity
    ): ConcreteTimeSlotTransfer;

    /**
     * Specification:
     *  - Checks if the zip code in the shipping address of the given quote matches the clause value.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ItemTransfer $itemTransfer
     * @param ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isSatisfiedByDeliveryArea(
        QuoteTransfer $quoteTransfer,
        ItemTransfer $itemTransfer,
        ClauseTransfer $clauseTransfer
    ): bool;

    /**
     * Specification:
     *  - Returns a list of discountable items, which are all items in the quote if the clause is satisfied.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return DiscountableItemTransfer[]
     */
    public function collectByDeliveryArea(
        QuoteTransfer $quoteTransfer,
        ClauseTransfer $clauseTransfer
    ): array;

    /**
     * Delivers the branch with the given code to the delivery area
     * identified by zip code
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliveryAreaByZipAndBranchCode(
        string $zipCode,
        string $branchCode
    ): bool;
}
