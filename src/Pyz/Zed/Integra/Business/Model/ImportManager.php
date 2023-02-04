<?php
/**
 * Durst - project - ImportManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 11:10
 */

namespace Pyz\Zed\Integra\Business\Model;

use DateTime;
use Exception;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Integra\Business\Exception\InvalidValueException;
use Pyz\Zed\Integra\Business\Model\Connection\WebServiceManagerInterface;
use Pyz\Zed\Integra\Business\Model\Log\LoggerInterface;
use Pyz\Zed\Integra\Business\Model\Order\OrderUpdaterInterface;
use Pyz\Zed\Integra\Business\Model\Quote\QuoteHydratorInterface;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTimeSlotRepositoryInterface;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTourRepositoryInterface;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class ImportManager implements ImportManagerInterface
{
    protected const DELIMITER_START_END_TIME = '%';

    protected const DEFAULT_DELIVERY_START_TIME = '1899-12-30T07:00:00.000+01:00';
    protected const DEFAULT_DELIVERY_END_TIME = '1899-12-30T15:00:00.000+01:00';

    protected const VALUE_NA = 'n/a';
    protected const INTEGRA_DEPOSIT_ITEM_TYPE = 190;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var WebServiceManagerInterface
     */
    protected $webServiceManager;

    /**
     * @var IntegraCredentialsInterface
     */
    protected $credentialsModel;

    /**
     * @var ConcreteTimeSlotRepositoryInterface
     */
    protected $concreteTimeSlotRepository;

    /**
     * @var ConcreteTourRepositoryInterface
     */
    protected $concreteTourRepository;

    /**
     * @var QuoteHydratorInterface
     */
    protected $quoteHydrator;

    /**
     * @var OrderUpdaterInterface
     */
    protected $orderUpdater;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IntegraConfig
     */
    protected $config;

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @var DepositFacadeInterface
     */
    protected $depositFacade;

    /**
     * @var array
     */
    protected $headerMapping = [];

    /**
     * @var array
     */
    protected $importData = [];

    /**
     * @var array
     */
    protected $productDepositMap = [];

    /**
     * @var array
     */
    protected $productUnitMap = [];

    /**
     * @var SpySalesOrder[]
     */
    protected $existingOrders = [];

    /**
     * @var int
     */
    protected $addedOrders = 0;

    /**
     * @var int
     */
    protected $updatedOrders = 0;

    /**
     * @var array
     */
    protected $recivedTourNos = [];

    /**
     * @var array
     */
    protected $toursToConcreteTimeslots = [];

    /**
     * ImportManager constructor.
     * @param SalesFacadeInterface $salesFacade
     * @param WebServiceManagerInterface $webServiceManager
     * @param IntegraCredentialsInterface $credentialsModel
     * @param ConcreteTimeSlotRepositoryInterface $concreteTimeSlotRepository
     * @param ConcreteTourRepositoryInterface $concreteTourRepository
     * @param QuoteHydratorInterface $quoteHydrator
     * @param OrderUpdaterInterface $orderUpdater
     * @param LoggerInterface $logger
     * @param IntegraConfig $config
     * @param IntegraQueryContainerInterface $queryContainer
     * @param OmsFacadeInterface $omsFacade
     * @param DepositFacadeInterface $depositFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade,
        WebServiceManagerInterface $webServiceManager,
        IntegraCredentialsInterface $credentialsModel,
        ConcreteTimeSlotRepositoryInterface $concreteTimeSlotRepository,
        ConcreteTourRepositoryInterface $concreteTourRepository,
        QuoteHydratorInterface $quoteHydrator,
        OrderUpdaterInterface $orderUpdater,
        LoggerInterface $logger,
        IntegraConfig $config,
        IntegraQueryContainerInterface $queryContainer,
        OmsFacadeInterface $omsFacade,
        DepositFacadeInterface $depositFacade
    ) {
        $this->salesFacade = $salesFacade;
        $this->webServiceManager = $webServiceManager;
        $this->credentialsModel = $credentialsModel;
        $this->concreteTimeSlotRepository = $concreteTimeSlotRepository;
        $this->concreteTourRepository = $concreteTourRepository;
        $this->quoteHydrator = $quoteHydrator;
        $this->orderUpdater = $orderUpdater;
        $this->logger = $logger;
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->omsFacade = $omsFacade;
        $this->depositFacade = $depositFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function importOrdersForBranch(int $idBranch): void
    {
        try {
            $credentials = $this
                ->credentialsModel
                ->getCredentialsByIdBranch($idBranch);

            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'starting tour import');
            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'starting deposit to product mapping');
            $this->mapProductsToDeposits($this
                ->webServiceManager
                ->getProductToDeposit($credentials));

            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'starting build units mapping');
            $this->mapProductsToUnits($this
                ->webServiceManager
                ->getProductMainUnitToSubUnit($credentials)
            );

            $responseData = $this
                ->webServiceManager
                ->importTours($credentials);

            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'mapping header');
            $this->mapHeader($responseData);

            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'parsing response');
            $this->parseResponse($responseData, $credentials);

            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'persisting orders');
            $this->persistOrders($idBranch);

            $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'setting tours as received');

            $this->updateOrderStatusIntegra($credentials->getFkBranch(), $this->recivedTourNos);
        } catch (Exception $e) {
            $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_ERROR, $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param int $idBranch
     *
     * @return void
     */
    protected function persistOrders(int $idBranch): void
    {
        $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'start persisting orders');
        foreach ($this->importData as $did => $order) {
            if (array_key_exists($order['reference'], $this->existingOrders) === true) {

                $this->updateExistingOrder($order);
                continue;
            }
            $this->addNewOrder($idBranch, $order);
        }
        $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, sprintf('%d new orders added', $this->addedOrders));
        $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, sprintf('%d orders updated', $this->updatedOrders));

        $this->addedOrders = 0;
        $this->updatedOrders = 0;
    }

    /**
     * @param int $idBranch
     * @param array $orderData
     *
     * @return void
     */
    protected function addNewOrder(int $idBranch, array $orderData): void
    {
        $quote = $this->quoteHydrator->getQuote($idBranch, $orderData);

        $this
            ->salesFacade
            ->saveSalesOrder($quote, $this->createSaveOrderTransfer());

        $order = $this
            ->queryContainer
            ->queryOrdersByReferences([$orderData['reference']])
            ->findOne();

        $this
            ->depositFacade
            ->saveOrderDeposit($quote, ($this->createSaveOrderTransfer())->setIdSalesOrder($order->getIdSalesOrder()));

        $this
            ->omsFacade
            ->triggerEventForNewOrderItems($this->getOrderItemIdsFromOrderRef($orderData['reference']));

        $this->addedOrders++;
    }

    /**
     * @param array $orderData
     *
     * @return void
     */
    protected function updateExistingOrder(array $orderData): void
    {
        $orderTransfer = $this
            ->salesFacade
            ->getOrderByIdSalesOrder($this->existingOrders[$orderData['reference']]->getIdSalesOrder());

        $orderTransfer = $this
            ->orderUpdater
            ->updateOrderTransferWithIntegraData($orderTransfer, $orderData);

        $this
            ->salesFacade
            ->updateOrder($orderTransfer, $orderTransfer->getIdSalesOrder());

        $this->updatedOrders++;
    }

    /**
     * @param int $idBranch
     *
     * @return void
     */
    protected function persistUpdatedOrders(int $idBranch): void
    {
        $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, 'start updating existing orders');
        $updated = 0;
        foreach ($this->existingOrders as $existingOrder) {
            if ($existingOrder->isModified() === true) {
                $existingOrder->save();
                $updated++;
            }
        }
        $this->logger->log($idBranch, LoggerInterface::LOG_LEVEL_INFO, sprintf('%d orders updated', $updated));
    }

    /**
     * @param int $idBranch
     * @param array $nrTourDeliveryArray
     */
    protected function updateOrderStatusIntegra(int $idBranch, array $nrTourDeliveryArray) : void
    {
        $credentials = $this
            ->credentialsModel
            ->getCredentialsByIdBranch($idBranch);

        foreach(array_unique($nrTourDeliveryArray) as $nrTourDelivery) {
            $this
                ->webServiceManager
                ->setImportedStatusForNrTourDelivery($credentials, $nrTourDelivery);
        }
    }

    /**
     * @return SaveOrderTransfer
     */
    protected function createSaveOrderTransfer(): SaveOrderTransfer
    {
        return new SaveOrderTransfer();
    }

    /**
     * @param array $responseData
     *
     * @return void
     */
    protected function mapHeader(array $responseData): void
    {
        $index = 0;
        foreach ($responseData['ColumnDescription']['Cells'] as $cell) {
            if (in_array($cell, ToursImportHeader::HEADER) !== true) {
                throw InvalidValueException::header($cell);
            }
            $this->headerMapping[$cell] = $index;
            $index++;
        }
    }

    /**
     * @param string $startTime
     * @param string $endTime
     *
     * @return string
     */
    protected function implodeStartEndTime(
        string $startTime,
        string $endTime
    ): string {
        return implode(
            static::DELIMITER_START_END_TIME,
            [$startTime, $endTime]
        );
    }

    /**
     * @param array $responseData
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return void
     */
    protected function parseResponse(array $responseData, IntegraCredentialsTransfer $credentials): void
    {
        $this->concreteTimeSlotRepository->resetCounter();
        $references = [];
        $tourReferences = [];
        $refsToDates = [];

        foreach ($responseData['Rows'] as $row) {

            if(in_array($this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]), $this->config->getBlacklistedProducts()) === true){
                continue;
            }

            if(array_key_exists($this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]), $this->config->getProductMapMissingNrme())){
                $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NRME]] = $this->config->getProductMapMissingNrme()[$this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]])];
            }

            if((int)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_RECEIPT_COUNT]]) === 0)
            {
                continue;
            }

            $didOrder = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_RECEIPT_DID]];
            $didPosition = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_POSITION_DID]];

            $deliveryDate = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_DELIVERY_DATE]];
            $timeSlotStart = $this->replaceIncorrectIntegraDateWithDeliveryDate($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TIME_SLOT_START]], $deliveryDate, self::DEFAULT_DELIVERY_START_TIME);
            $timeSlotEnd = $this->replaceIncorrectIntegraDateWithDeliveryDate($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TIME_SLOT_END]], $deliveryDate, self::DEFAULT_DELIVERY_END_TIME);

            if($timeSlotStart === null && $timeSlotEnd === null){
                $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, sprintf("skipped integra order no %s, because of missing start and end time", $this->headerMapping[ToursImportHeader::HEADER_NO]));
                continue;
            }

            if($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_POSITION_TYPE]] == static::INTEGRA_DEPOSIT_ITEM_TYPE)
            {
                $this->importData[$didOrder]['deposits'][$this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]])] = [
                    'merchant_sku' => $this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]),
                    'quantity' => (int)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_RECEIPT_COUNT]]),
                    'tax_rate' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TAX_RATE]]),
                    'tax_amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TAX_AMOUNT]]),
                    'net_amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_AMOUNT]]),
                    'amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_BETRAGSKOFAEHIG]]),
                    'unit_type' => $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NRME]],
                ];
                continue;
            }

            /**
             * TODO : temporarily skip all orders that do not have a externnummer (GBZ orders, not via durst)
             */
            if($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_EXTERNNUMMER]] === null)
            {
               // continue;
            }

            $reference = $this->getDurstOrIntegraRef(
                $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_EXTERNNUMMER]],
                $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_EVENT_NO]]
            );
            $tourReference = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NR_TOURLIEF]];
            $tourReference = $this->prefixReference($tourReference);
            $reference = $this->prefixReference($reference);
            $references[] = $reference;
            $tourReferences[] = $tourReference;
            $didPosition = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_POSITION_DID]];
            $customerNo = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_CUSTOMER_NO]];
            $nrTourLief = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NR_TOURLIEF]];
            $versandArt = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_SHIPPING_TYPE_NO]];
            $zahlArt = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_PAYMENT_TYPE_NO]];
            $nrTour = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TOUR_NO]];
            $nrTourFolge = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TOUR_SEQUENCE]];

            $this->importData[$didOrder]['items'][$didPosition] = [
                'name' => $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_DESCRIPTION]],
                'merchant_sku' => $this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]),
                'quantity' => (int)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_RECEIPT_COUNT]]),
                'tax_rate' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TAX_RATE]]),
                'tax_amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_TAX_AMOUNT]]),
                'net_amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_AMOUNT]]),
                'amount' => (float)($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_BETRAGSKOFAEHIG]]),
                'lfd_unit' => $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_LFDNRVERPEINH]],
                'quantity_unit' => $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_PACKAGING_COUNT]],
                'unit_type' => $this->getUnitNameForProductAndLfdNr(
                        $this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]),
                        $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_LFDNRVERPEINH]],
                        $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NRME]]
                    ),
                'deposit_id' => (int) $this->getDepositSkuForProduct($this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NO]]), $this->removeLeadingZeros($row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_NRME]])),
            ];
            $this->importData[$didOrder]['customer_no'] = $customerNo;
            $this->importData[$didOrder][ToursImportHeader::HEADER_TIME_SLOT_START] = $timeSlotStart;
            $this->importData[$didOrder][ToursImportHeader::HEADER_TIME_SLOT_END] = $timeSlotEnd;
            $this->importData[$didOrder]['did'] = $didOrder;

            $customerResponse = $this->webServiceManager->getCustomer($credentials, $customerNo);
            $this->importData[$didOrder]['salutation'] = $customerResponse['Anrede'];
            $this->importData[$didOrder]['first_name'] = $this->checkIfValueOrNa($customerResponse['Name1']);
            $this->importData[$didOrder]['last_name'] = $this->checkIfValueOrNa($customerResponse['Name2']);
            $this->importData[$didOrder]['address1'] = $customerResponse['StrasseNr'];
            $this->importData[$didOrder]['zip'] = $customerResponse['Plz'];
            $this->importData[$didOrder]['city'] = $customerResponse['Ort'];
            $this->importData[$didOrder]['email'] = $this->checkIfValueOrNa($customerResponse['Mail']);
            $this->importData[$didOrder]['phone'] = $this->checkIfValueOrNa($customerResponse['Telefon']);
            $this->importData[$didOrder]['b2c'] = $this->getIsB2C($customerResponse['PreisGrp']);
            $this->importData[$didOrder]['reference'] = $reference;
            $this->importData[$didOrder]['nrTourLief'] = $nrTourLief;
            $this->importData[$didOrder]['versandArt'] = $versandArt;
            $this->importData[$didOrder]['zahlArt'] = $zahlArt;
            $this->importData[$didOrder]['nrTour'] = $nrTour;
            $this->importData[$didOrder]['nrTourFolge'] = $nrTourFolge;
            $this->importData[$didOrder]['receiptNo'] = $row['Cells'][$this->headerMapping[ToursImportHeader::HEADER_RECEIPT_NO]];

            $fkConcreteTimeSlot = $this->concreteTimeSlotRepository->getTimeSlotId(
                trim($customerResponse['Plz']),
                $credentials->getFkBranch(),
                $timeSlotStart,
                $timeSlotEnd
            );

            $this->importData[$didOrder]['fk_concrete_time_slot'] = $fkConcreteTimeSlot;
            $this->toursToConcreteTimeslots[$tourReference][] = $fkConcreteTimeSlot;
            $this->recivedTourNos[] = $nrTourLief;
            $refsToDates[$tourReference] =  $timeSlotStart;
        }

        $this->mapDepositsToItems($this->importData);

        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'loading existing orders');
        $this->loadExistingOrders($references);
        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'existing orders loaded');

        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'loading existing tours');
        $message = sprintf('%d new concrete tours created', $this->concreteTourRepository->loadTours($tourReferences, $credentials->getFkBranch(), $refsToDates));
        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, $message);

        $message = sprintf('%d new concrete time slots created', $this->concreteTimeSlotRepository->getCounter());
        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, $message);

        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'update concrete time slots with tour fk');
        $this->updateConcreteTimeSlotsWithTourFk();
        $this->logger->log($credentials->getFkBranch(), LoggerInterface::LOG_LEVEL_INFO, 'end update concrete time slots with tour fk');
    }

    /**
     * @param string|null $durstRef
     * @param string|null $integraRef
     * @return string
     */
    protected function getDurstOrIntegraRef(?string $durstRef, ?string $integraRef) : string
    {
        if($durstRef !== null){
            return $durstRef;
        }

        return $integraRef;
    }

    /**
     * @param string $reference
     *
     * @return string
     */
    protected function prefixReference(string $reference): string
    {
        if (strpos($reference, 'DE') === false) {
            return sprintf('%s%s', IntegraConstants::INTEGRA_REFERENCE_PREFIX, $reference);
        }

        return $reference;
    }

    /**
     * @param array $references
     *
     * @return void
     */
    protected function loadExistingOrders(array $references): void
    {
        if (count($references) < 1) {
            return;
        }

        $entities = $this
            ->queryContainer
            ->queryOrdersByReferences($references)
            ->find();

        foreach ($entities as $entity) {
            $this->existingOrders[$entity->getOrderReference()] = $entity;
        }
    }

    /**
     * @param string $string
     * @return string
     */
    protected function removeLeadingZeros(string $string) : string
    {
        return ltrim($string, '0');
    }

    /**
     * @param string|null $value
     * @return string
     */
    protected function checkIfValueOrNa(?string $value) : string
    {
        if($value !== null && $value !== ''){
            return $value;
        }

        return static::VALUE_NA;
    }

    /**
     * @throws PropelException
     */
    protected function updateConcreteTimeSlotsWithTourFk() {
        foreach ($this->toursToConcreteTimeslots as $tourRef => $concreteTimeslot)
        {
            $concreteTimeslotEntities = $this
                ->queryContainer
                ->queryConcreteTimeSlotsByIdsInArray(array_unique($concreteTimeslot))
                ->find();

            foreach ($concreteTimeslotEntities as $concreteTimeslotEntity){
                $concreteTimeslotEntity
                    ->setFkConcreteTour($this->concreteTourRepository->getTourIdByReference($tourRef))
                    ->save();
            }
        }
    }

    /**
     * @param string|null $falseDate
     * @param string $deliveryDate
     * @param string $default
     * @return string|null
     */
    protected function replaceIncorrectIntegraDateWithDeliveryDate(?string $falseDate, string $deliveryDate, string $default) : ?string
    {
        if($falseDate === null){
            $falseDate = $default;
        }

        $actualDate = substr($deliveryDate, 0, 10);
        $newDateTimeString =  str_replace(substr($falseDate, 0, 10), $actualDate, $falseDate);

        $date = new DateTime(substr($newDateTimeString, 0, 16).'Europe/Berlin');

        if($date->format('I') == 1)
        {
            $newDateTimeString = str_replace('+01:00', '+02:00', $newDateTimeString);
        }

        return $newDateTimeString;
    }

    /**
     * @param string $orderRef
     * @return array
     */
    protected function getOrderItemIdsFromOrderRef(string $orderRef) : array
    {
        return array_values($this
            ->queryContainer
            ->queryOrderItemIdsByOrderReferences($orderRef)
            ->find()
            ->toArray());
    }

    /**
     * @param array $productDepositResponse
     */
    protected function mapProductsToDeposits(array $productDepositResponse) : void
    {
        foreach($productDepositResponse['Rows'] as $row)
        {
            $productSku = (string) $this->removeLeadingZeros($row['Cells'][0]);
            $unit = $row['Cells'][1];
            $depositSku = $this->removeLeadingZeros($row['Cells'][2]);

            if(array_key_exists($productSku, $this->productDepositMap) !== true)
            {
                $this->productDepositMap[$productSku] = [];
                $this->productDepositMap[$productSku][$unit] = $depositSku;

                continue;
            }

            $this->productDepositMap[$productSku][$unit] = $depositSku;
        }
    }

    /**
     * @param string $product
     * @param string $unit
     * @return string|null
     */
    protected function getDepositSkuForProduct(string $product, string $unit) : ?string
    {
        return $this->productDepositMap[$product][$unit] ?? null;
    }

    /**
     * @param array $productUnitsResponse
     */
    protected function mapProductsToUnits(array $productUnitsResponse) : void
    {
        foreach($productUnitsResponse['Rows'] as $row)
        {
            $productSku = (string) $this->removeLeadingZeros($row['Cells'][0]);
            $lfdUnit = $row['Cells'][1];
            $unitName = $row['Cells'][4];

            if(array_key_exists($productSku, $this->productUnitMap) !== true)
            {
                $this->productUnitMap[$productSku] = [];
                $this->productUnitMap[$productSku][$lfdUnit] = $unitName;

                continue;
            }

            $this->productUnitMap[$productSku][$lfdUnit] = $unitName;
        }
    }


    /**
     * @param string $product
     * @param string|null $lfdNr
     * @param string $defaultUnit
     * @return string
     */
    protected function getUnitNameForProductAndLfdNr(string $product, ?string $lfdNr, string $defaultUnit) : string
    {
        if($lfdNr === null){
            return $defaultUnit;
        }

        return $this->productUnitMap[$product][$lfdNr];
    }

    /**
     * @param array $orderItems
     */
    protected function mapDepositsToItems(array &$orderItems) : void
    {
        foreach ($orderItems as $did => $orderItem){
            foreach ($orderItem['items'] as $pdid => $item)
            {
                if($orderItems[$did]['items'][$pdid]['deposit_id'] == 0){
                    $orderItems[$did]['items'][$pdid]['deposit'] = [];
                    continue;
                }

                $orderItems[$did]['items'][$pdid]['deposit'] = $orderItems[$did]['deposits'][$orderItems[$did]['items'][$pdid]['deposit_id']];
            }
        }
    }

    /**
     * @param string|null $priceGroup
     * @return bool
     */
    protected function getIsB2C(?string $priceGroup) : bool
    {
        if(in_array($priceGroup, ['05', '06']) === true){
            return true;
        }

        return false;
    }
}
