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
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\Business\Exception\BranchNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * @method DeliveryAreaBusinessFactory getFactory()
 */
class DeliveryAreaFacade extends AbstractFacade implements DeliveryAreaFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deliveryAreasAreImported()
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaWriter()
            ->deliveryAreasAreImported();
    }

    /**
     * {@inheritDoc}
     *
     * @param $idConcreteTimeSlot
     *
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotByIdIgnoreActive(int $idConcreteTimeSlot) : ConcreteTimeSlotTransfer
    {
        return $this
            ->getFactory()
            ->createTimeSlotFinder()
            ->getConcreteTimeSlotByIdIgnoreActive($idConcreteTimeSlot);
    }

    /**
     * {@inheritdoc}
     *
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     */
    public function createDeliveryArea(DeliveryAreaTransfer $deliveryAreaTransfer)
    {
        $this
            ->getFactory()
            ->createDeliveryAreaWriter()
            ->createDeliveryArea($deliveryAreaTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     */
    public function createTimeSlot(TimeSlotTransfer $timeSlotTransfer)
    {
        $this
            ->getFactory()
            ->createTimeSlotWriter()
            ->createTimeSlot($timeSlotTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function timeSlotsAreImported()
    {
        return $this
            ->getFactory()
            ->createTimeSlotWriter()
            ->timeSlotsAreImported();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @param string $city
     * @param int $zip
     * @return DeliveryAreaTransfer
     */
    public function addDeliveryArea($name, $city, $zip)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->addDeliveryArea($name, $city, $zip);
    }

    /**
     * {@inheritdoc}
     *
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     * @return DeliveryAreaTransfer
     */
    public function updateDeliveryArea(DeliveryAreaTransfer $deliveryAreaTransfer)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->save($deliveryAreaTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeliveryArea
     * @return void
     */
    public function removeDeliveryArea($idDeliveryArea)
    {
        $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->removeDeliveryArea($idDeliveryArea);
    }

    /**
     * {@inheritdoc}
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     * @return TimeSlotTransfer
     */
    public function updateTimeSlot(TimeSlotTransfer $timeSlotTransfer)
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->save($timeSlotTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param TimeSlotTransfer $timeSlotTransfer
     * @return TimeSlotTransfer
     */
    public function addTimeSlot(TimeSlotTransfer $timeSlotTransfer)
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->save($timeSlotTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTimeSlot
     * @return void
     */
    public function removeTimeSlot($idTimeSlot)
    {
        $this
            ->getFactory()
            ->createTimeSlotModel()
            ->removeTimeSlot($idTimeSlot);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeliveryArea
     * @return bool
     */
    public function hasDeliveryArea($idDeliveryArea)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->hasDeliveryArea($idDeliveryArea);
    }

    /**
     * {@inheritdoc}
     *
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreas()
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreas();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeliveryArea
     * @return DeliveryAreaTransfer
     */
    public function getDeliveryAreaById($idDeliveryArea)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreaById($idDeliveryArea);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranch($idBranch)
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->getTimeSlotsByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param string $weekday
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchAndWeekday(int $idBranch, string $weekday): array
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->getTimeSlotsByIdBranchAndWeekday($idBranch, $weekday);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param string $weekday
     * @param int $idAbstractTour
     *
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotsByIdBranchWeekdayAndAbstractTour(int $idBranch, string $weekday, int $idAbstractTour): array
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->getTimeSlotsByIdBranchWeekdayAndAbstractTour($idBranch, $weekday, $idAbstractTour);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idTimeSlot
     * @return TimeSlotTransfer
     */
    public function getTimeSlotById($idTimeSlot)
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->getTimeSlotById($idTimeSlot);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasByIdBranch($idBranch)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreasByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $idDeliveryArea
     * @return TimeSlotTransfer[]
     */
    public function getTimeSlotByIdBranchAndIdDeliveryArea($idBranch, $idDeliveryArea)
    {
        return $this
            ->getFactory()
            ->createTimeSlotModel()
            ->getTimeSlotByIdBranchAndIdDeliverArea($idBranch, $idDeliveryArea);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $zip
     * @param string $name
     * @return DeliveryAreaTransfer
     * zip code and name exists in the database
     */
    public function getDeliveryAreaByZipAndName($zip, $name)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreaByZipAndName($zip, $name);
    }

    /**
     * {@inheritdoc}
     *
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasWithoutTimeSlots()
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreasWithoutTimeSlots();
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return ConcreteTimeSlotTransfer|SpyConcreteTimeSlotEntityTransfer
     */
    public function createConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer)
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotWriter()
            ->createConcreteTimeSlot($concreteTimeSlotTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTimeSlot
     * @return ConcreteTimeSlotTransfer
     * @throws AmbiguousComparisonException
     * @throws Exception\ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function getConcreteTimeSlotById(int $idConcreteTimeSlot): ConcreteTimeSlotTransfer
    {
        return $this
            ->getFactory()
            ->createTimeSlotFinder()
            ->getConcreteTimeSlotById($idConcreteTimeSlot);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTime|int $start
     * @param DateTime $end
     * @return ConcreteTimeSlotTransfer
     * @throws AmbiguousComparisonException
     * @throws Exception\ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function getConcreteTimeSlotForBranchByStartAndEnd(int $idBranch, DateTime $start, DateTime $end): ConcreteTimeSlotTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->getConcreteTimeSlotByIdBranchAndStartAndEnd($idBranch, $start, $end);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $zipCode
     * @return DeliveryAreaTransfer
     */
    public function getDeliveryAreaByZipCode(string $zipCode): DeliveryAreaTransfer
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreaByZipCode($zipCode);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return DeliveryAreaTransfer
     * @throws BranchNotFoundException
     * @throws DeliveryAreaNotFoundException
     */
    public function getDeliveryAreaByZipOrBranchCode(
        string $zipCode,
        string $branchCode
    ): DeliveryAreaTransfer
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreaByZipOrBranchCode(
                $zipCode,
                $branchCode
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @param string $zipCode
     * @return ConcreteTimeSlotTransfer[]
     */
    public function getConcreteTimeSlotsForBranchesAndZipCode(array $branchIds, string $zipCode, int $maxSlots, int $itemsPerSlot): array
    {
        return $this
            ->getFactory()
            ->createTimeSlotFinder()
            ->getTimeSlotsForBranchesAndZipCode($branchIds, $zipCode, $maxSlots, $itemsPerSlot);
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItemsByDeliveryCost(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this
            ->getFactory()
            ->createTimeSlotManager()
            ->expandItemsByDeliveryCost($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateDeliveryCostTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createDeliveryCostCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItemsByMinValue(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this
            ->getFactory()
            ->createMinValueExpander()
            ->expandItemsByMinValue($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItemsByMinUnits(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this
            ->getFactory()
            ->createMinUnitsExpander()
            ->expandItemsByMinUnits($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateMissingMinValueTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createMissingMinValueCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateMissingMinUnits(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createMissingMinUnitsCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkConcreteTimeSlotAssertionsForCheckout(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->checkConcreteTimeSlotAssertions($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkZipCodesDeliveyAddressTimeSlotMatch(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->checkZipCodeCondition($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateConcreteTimeSlot(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotHydrator()
            ->hydrateOrderTransferWithConcreteTimeSlotTransfer($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateDeliveryCostTaxRate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createDeliveryCostTaxRateCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     */
    public function saveOrderDeliveryCost(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this
            ->getFactory()
            ->createDeliveryCostOrderSaver()
            ->saveOrderShipment($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $deliveryareaData
     * @param LocaleTransfer $locale
     * @return PageMapTransfer
     */
    public function buildDeliveryAreaPageMap(PageMapBuilderInterface $pageMapBuilder, array $deliveryareaData, LocaleTransfer $locale): PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $deliveryareaData, $locale);
    }

    /**
     * {@inheritdoc}
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $timeslotData
     * @param LocaleTransfer $locale
     * @return PageMapTransfer
     */
    public function buildTimeslotPageMap(PageMapBuilderInterface $pageMapBuilder, array $timeslotData, LocaleTransfer $locale): PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createTimeslotDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $timeslotData, $locale);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function createFutureConcreteTimeSlots()
    {
        $this
            ->getFactory()
            ->createConcreteTimeSlotCreator()
            ->createConcreteTimeSlots();
    }

    /**
     * {@inheritdoc}
     *
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     */
    public function createFormattedTimeSlotString(
        DateTime $start,
        DateTime $end
    ): string {
        return $this
            ->getFactory()
            ->createTimeSlotFinder()
            ->createFormattedTimeSlotString($start, $end);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function touchDeletePassedConcreteTimeSlots()
    {
        $this
            ->getFactory()
            ->createPassedConcreteTimeSlotDeleteToucher()
            ->touchPassedConcreteTimeSlotsToDelete();
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartPreCheckResponseTransfer
     */
    public function validateConcreteTimeSlotAssertionsForCheckout(CartChangeTransfer $cartChangeTransfer): CartPreCheckResponseTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->validateConcreteTimeSlotAssertions($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTimeSlotTransfer
     * @throws Exception\ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function setFkConcreteTourInConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTimeSlotTransfer
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->setFkConcreteTourInConcreteTimeSlot($concreteTimeSlotTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @return ConcreteTimeSlotTransfer[]
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getConcreteTimeSlotsInFutureWithoutConcreteTour() : array
    {
        return $this
            ->getFactory()
            ->createTimeSlotFinder()
            ->getConcreteTimeSlotsInFutureWithoutConcreteTour();
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     *
     * @return bool
     */
    public function runConcreteTimeSlotTouchPostSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): bool
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotTouchPostSaveHook()
            ->touchConcreteTimeSlots($quoteTransfer, $checkoutResponse);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param array $emails
     * @param int $page
     * @param string|null $filename
     *
     * @return void
     */
    public function createTimeSlotExportAndSendToEmailForBranch(
        int $idBranch,
        array $emails,
        int $page = 1,
        ?string $filename = null
    ): void {
        $this
            ->getFactory()
            ->createCsvTimeSlotExporter()
            ->writeChunk($idBranch, $emails, $page, $filename);
    }

    /**
     * @param string $importJson
     * @return void
     */
    public function importTimeSlotsForBranchByCsv(string $importJson)
    {
        return $this
            ->getFactory()
            ->createTimeSlotCsvImporter()
            ->importTimeSlotsFromCsv($importJson);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTimeSlot
     * @param int $idBranch
     * @return void
     */
    public function deleteConcreteTimeSlotByIdAndBranch(
        int $idConcreteTimeSlot,
        int $idBranch
    ): void
    {
        $this
            ->getFactory()
            ->createConcreteTimeSlotDeleteToucher()
            ->deleteConcreteTimeSlotByIdAndBranch(
                $idConcreteTimeSlot,
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SpyDeliveryArea $deliveryAreaEntity
     * @return DeliveryAreaTransfer
     */
    public function convertDeliveryAreaEntityToTransfer(SpyDeliveryArea $deliveryAreaEntity): DeliveryAreaTransfer
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->entityToTransfer($deliveryAreaEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return ConcreteTimeSlotTransfer
     */
    public function convertConcreteTimeSlotEntityToTransfer(
        SpyConcreteTimeSlot $concreteTimeSlotEntity
    ): ConcreteTimeSlotTransfer {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotModel()
            ->entityToTransfer($concreteTimeSlotEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliveryAreaByZipAndBranchCode(string $zipCode, string $branchCode): bool
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaModel()
            ->getDeliveryAreaByZipAndBranchCode(
                $zipCode,
                $branchCode
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ItemTransfer $itemTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return bool
     */
    public function isSatisfiedByDeliveryArea(
        QuoteTransfer $quoteTransfer,
        ItemTransfer $itemTransfer,
        ClauseTransfer $clauseTransfer
    ): bool {
        return $this
            ->getFactory()
            ->createDeliveryAreaDecisionRule()
            ->isSatisfiedBy($quoteTransfer, $clauseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ClauseTransfer $clauseTransfer
     *
     * @return array
     */
    public function collectByDeliveryArea(
        QuoteTransfer $quoteTransfer,
        ClauseTransfer $clauseTransfer
    ): array {
        return $this
            ->getFactory()
            ->createDeliveryAreaCollector()
            ->collect($quoteTransfer, $clauseTransfer);
    }
}
