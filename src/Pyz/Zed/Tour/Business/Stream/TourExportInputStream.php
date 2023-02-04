<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 15:16
 */

namespace Pyz\Zed\Tour\Business\Stream;

use ArrayIterator;
use ArrayObject;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\Merchant\Persistence\Base\SpyBranch;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;

class TourExportInputStream implements StreamInterface, ReadStreamInterface
{

    public const BILLING_DATE_FORMAT = 'Y-m-d';

    /**
     * @var int
     */
    protected $idTour;

    /**
     * @var OmsQueryContainerInterface
     */
    protected $omsQueryContainer;

    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * @var TourQueryContainerInterface
     */
    protected $tourQueryContainer;

    /**
     * @var ProductQueryContainer
     */
    protected $productQueryContainer;

    /**
     * @var EdifactReferenceGenerator
     */
    protected $sequenceGenerator;

    /**
     * @var BillingFacadeInterface
     */
    protected $billingFacade;

    /**
     * @var DateTime
     */
    protected $currentDate;

    /**
     * @var ArrayIterator
     */
    protected $iterator;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactFacade;

    /**
     * @var string
     */
    protected $exportVersion;

    /**
     * TourExportInputStream constructor.
     * @param int $idTour
     * @param OmsQueryContainerInterface $omsQueryContainer
     * @param TourConfig $tourConfig
     * @param TourQueryContainerInterface $tourQueryContainer
     * @param ProductQueryContainer $productQueryContainer
     * @param EdifactReferenceGenerator $edifactReferenceGenerator
     * @param BillingFacadeInterface $billingFacade
     * @param EdifactFacadeInterface $edifactFacade
     * @throws Exception
     */
    public function __construct(
        int $idTour,
        OmsQueryContainerInterface $omsQueryContainer,
        TourConfig $tourConfig,
        TourQueryContainerInterface $tourQueryContainer,
        ProductQueryContainer $productQueryContainer,
        EdifactReferenceGenerator $edifactReferenceGenerator,
        BillingFacadeInterface $billingFacade,
        EdifactFacadeInterface $edifactFacade
    )
    {
        $this->idTour = $idTour;
        $this->omsQueryContainer = $omsQueryContainer;
        $this->tourConfig = $tourConfig;
        $this->tourQueryContainer = $tourQueryContainer;
        $this->productQueryContainer = $productQueryContainer;
        $this->sequenceGenerator = $edifactReferenceGenerator;
        $this->billingFacade = $billingFacade;
        $this->edifactFacade = $edifactFacade;

        $this->setCurrentDate();
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return $this
            ->get();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $currentItem = $this
            ->iterator
            ->current();

        $this
            ->iterator
            ->next();

        return $currentItem;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function open(): bool
    {
        /*  @var $concreteTour DstConcreteTour */
        $concreteTour = $this
            ->tourQueryContainer
            ->queryConcreteTour()
            ->joinWith('DstAbstractTour dat')
            ->joinWith('SpyConcreteTimeSlot scts')
            ->joinWith('scts.SpyTimeSlot sts')
            ->joinWith('SpyBranch b')
            ->joinWith('sts.SpyDeliveryArea sda')
            ->findByIdConcreteTour($this->idTour)
            ->getFirst();

        $this->exportVersion = $this->edifactFacade->getExportVersion();

        $items = $this->findTourOrderItems();

        $exportArray = [];

        if ($concreteTour->getIdConcreteTour() !== null) {
            $concreteTourData = $this
                ->getTourDataFromEntity($concreteTour);

            $orderItems = $this
                ->getOrderItemDataFromSalesOrderItems($items);

            if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
                $exportArray = $this
                    ->consolidateProductsBySku($orderItems, $concreteTourData);
            } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
                $orders = $this->getTourOrders($items);

                $orderArray = $this->getOrderDataFromSalesOrders($orders);

                $exportArray = $this
                    ->mergeOrdersAndItems($orderArray, $orderItems, $concreteTourData);
            }
        }

        $this->iterator = (new ArrayObject($exportArray))
            ->getIterator();

        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        unset($this->iterator);

        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return int
     */
    public function seek(int $offset, int $whence): int
    {
        if ($whence === SEEK_SET && $this->iterator->count() > 0) {
            $this
                ->iterator
                ->seek($offset);

            return 0;
        }

        return 1;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        return !$this
            ->iterator
            ->valid();
    }

    /**
     * @param int $minutes
     * @return DateInterval
     * @throws Exception
     */
    protected function createDateInterval(int $minutes) : DateInterval
    {
        $dateIntervalString = sprintf(TourConfig::TIME_INTERVAL_TEMPLATE, $minutes);
        return new DateInterval($dateIntervalString);
    }

    /**
     * @return array
     */
    protected function getStateIds(): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName($this->tourConfig->getAcceptedOmsState())
            ->find();

        $states = [];
        foreach ($result as $row) {
            $states[] = $row->getIdOmsOrderItemState();
        }

        return $states;
    }

    /**
     * @return array
     */
    protected function getProcesses(): array
    {
        $activeProcesses = $this->getActiveProcesses();

        $processes = [];

        foreach ($activeProcesses as $process) {
            $processes[$process->getIdOmsOrderProcess()] = $process->getName();
        }

        asort($processes);

        return $processes;
    }

    /**
     * @return ObjectCollection
     */
    protected function getActiveProcesses(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->getActiveProcesses($this->tourConfig->getActiveProcesses())
            ->find();
    }

    /**
     * @param DstConcreteTour $concreteTour
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function getTourDataFromEntity(DstConcreteTour $concreteTour): array
    {
        $branch = $concreteTour
            ->getSpyBranch();

        $abstractTour = $concreteTour
            ->getDstAbstractTour();

        $projectTimezone = new DateTimeZone($this->tourConfig->getProjectTimeZone());

        $deliveryStartTimes = [];

        /*  @var $concreteTimeSlots SpyConcreteTimeSlot[] */
        $concreteTimeSlots = $concreteTour
            ->getSpyConcreteTimeSlots();

        foreach ($concreteTimeSlots as $concreteTimeSlot) {
            $deliveryStartDateTime = $concreteTimeSlot
                ->getStartTime();
            $deliveryStartDateTime
                ->setTimezone($projectTimezone);
            $deliveryStartTimes[] = $deliveryStartDateTime;
        }

        usort($deliveryStartTimes, function ($a, $b) {
            if ($a === $b) {
                return 0;
            } else if ($a < $b) {
                return -1;
            }

            return 1;
        });

        $deliveryStartDateTime = reset($deliveryStartTimes);

        $timeLoading = $this
            ->createDateInterval(0);
        $timeApproach = $this
            ->createDateInterval(0);

        $deliveryStartDateTime
            ->sub($timeApproach)
            ->sub($timeLoading);

        return [
            TourExportMapper::PAYLOAD_CREATE_DATE => $this->currentDate->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_TIME => $this->currentDate->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_DATETIME => $this->currentDate->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATE => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_TIME => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATETIME => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DESCRIPTION_RETURN_ITEM => 'REASON_FOR_RETURN',
            TourExportMapper::PAYLOAD_DRIVER => 'DRIVER_HERE',
            TourExportMapper::PAYLOAD_BILLING_REFERENCE => $this->getCurrentBillingReferenceForBranchById($branch->getIdBranch()),
            TourExportMapper::PAYLOAD_ILN_RECIPIENT => $this->tourConfig->getDurstIlnNumber(),
            TourExportMapper::PAYLOAD_ILN_SENDER => $branch->getGln(),
            TourExportMapper::PAYLOAD_ILN_DELIVERY => $this->getDurstGln($branch),
            TourExportMapper::PAYLOAD_IS_RETURN_ITEM => false,
            TourExportMapper::PAYLOAD_TOUR_NUMBER => $concreteTour->getTourReference(),
            TourExportMapper::PAYLOAD_ACCESS_TOKEN => $branch->getAccessToken(),
            TourExportMapper::PAYLOAD_DATA_TRANSFER_REFERENCE => $this->sequenceGenerator->generateDataTransferReference($branch),
            TourExportMapper::PAYLOAD_MESSAGE_REFERENCE => $this->sequenceGenerator->generateMessageReference($branch),
            TourExportMapper::PAYLOAD_BASIC_AUTH_USERNAME => $branch->getBasicAuthUsername(),
            TourExportMapper::PAYLOAD_BASIC_AUTH_PASSWORD => $branch->getBasicAuthPassword()
        ];
    }

    /**
     * @param ObjectCollection $orders
     * @return array
     */
    protected function getOrderDataFromSalesOrders(ObjectCollection $orders): array
    {
        $orderArray = [];

        /* @var $order SpySalesOrder */
        foreach ($orders as $order) {
            $orderArray[$order->getIdSalesOrder()] = [
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => $order->getOrderReference(),
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => $order->getDurstCustomerReference() ?? null
            ];
        }

        ksort($orderArray);

        return $orderArray;
    }

    /**
     * @param ObjectCollection $orderItems
     * @return array
     * @throws PropelException
     */
    protected function getOrderItemDataFromSalesOrderItems(ObjectCollection $orderItems): array
    {
        $orderItemArray = [];

        /*  @var $orderItem SpySalesOrderItem */
        foreach ($orderItems as $orderItem) {
            $orderItemArray[$orderItem->getMerchantSku()][] = [
                TourExportMapper::PAYLOAD_QUANTITY => $orderItem->getQuantity(),
                TourExportMapper::PAYLOAD_DURST_SKU => $orderItem->getSku(),
                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => $orderItem->getName(),
                TourExportMapper::PAYLOAD_GTIN => $orderItem->getMerchantSku(),
                TourExportMapper::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE => $orderItem->getOrder()->getOrderReference(),
                TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY => $orderItem->getPriceToPayAggregation(),
            ];
        }

        ksort($orderItemArray);

        return $orderItemArray;
    }

    /**
     * @param array $allProducts
     * @param array $tourData
     * @return array
     */
    protected function consolidateProductsBySku(array $allProducts, array $tourData): array
    {
        $exportData = [];

        $exportData[] = array_merge($tourData, [
            TourExportMapper::PAYLOAD_MERCHANT_SKU => null,
            TourExportMapper::PAYLOAD_QUANTITY => null,
            TourExportMapper::PAYLOAD_DURST_SKU => null,
            TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => null,
            TourExportMapper::PAYLOAD_GTIN => null
        ]);

        foreach ($allProducts as $sku => $productData) {
            $quantities = array_column($productData, TourExportMapper::PAYLOAD_QUANTITY);
            $durstSkus = array_unique(array_column($productData, TourExportMapper::PAYLOAD_DURST_SKU));
            $productDescription = array_unique(array_column($productData, TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION));
            $gtin = array_unique(array_column($productData, TourExportMapper::PAYLOAD_GTIN));

            $sum = 0;

            foreach ($quantities as $quantity) {
                $sum += $quantity;
            }
            $exportData[] = array_merge($tourData, [
                TourExportMapper::PAYLOAD_MERCHANT_SKU => $sku,
                TourExportMapper::PAYLOAD_QUANTITY => $sum,
                TourExportMapper::PAYLOAD_DURST_SKU => reset($durstSkus),
                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => reset($productDescription),
                TourExportMapper::PAYLOAD_GTIN => reset($gtin)
            ]);
        }

        return $exportData;
    }

    /**
     * @param array $orders
     * @param array $orderItems
     * @param array $tourData
     * @return array
     */
    protected function mergeOrdersAndItems(array $orders, array $orderItems, array $tourData): array
    {
        $exportData = [];

        $exportData[] = array_merge($tourData, [
            TourExportMapper::PAYLOAD_ORDER_REFERENCE => null,
            TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => null,
            TourExportMapper::PAYLOAD_ORDER_ITEMS => null,
        ]);

        foreach ($orders as $order) {
            $mergedOrderItems = [];

            foreach ($orderItems as $sku => $skuItems) {
                foreach($skuItems as $orderItem) {
                    if ($orderItem[TourExportMapper::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE] === $order[TourExportMapper::PAYLOAD_ORDER_REFERENCE])
                    {
                        if (!in_array(
                            $orderItem[TourExportMapper::PAYLOAD_DURST_SKU],
                            array_keys($mergedOrderItems)
                        )) {
                            $mergedOrderItems[$orderItem[TourExportMapper::PAYLOAD_DURST_SKU]] = [
                                TourExportMapper::PAYLOAD_MERCHANT_SKU => $sku,
                                TourExportMapper::PAYLOAD_QUANTITY => $orderItem[TourExportMapper::PAYLOAD_QUANTITY],
                                TourExportMapper::PAYLOAD_DURST_SKU => $orderItem[TourExportMapper::PAYLOAD_DURST_SKU],
                                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => $orderItem[TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION],
                                TourExportMapper::PAYLOAD_GTIN => $orderItem[TourExportMapper::PAYLOAD_GTIN],
                                TourExportMapper::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE => $orderItem[TourExportMapper::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE],
                                TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY=> $orderItem[TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY],
                            ];
                        } else {
                            $mergedOrderItems[$orderItem[TourExportMapper::PAYLOAD_DURST_SKU]][TourExportMapper::PAYLOAD_QUANTITY] += $orderItem[TourExportMapper::PAYLOAD_QUANTITY];
                            $mergedOrderItems[$orderItem[TourExportMapper::PAYLOAD_DURST_SKU]][TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY] += $orderItem[TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY];
                        }
                    }
                }
            }

            $order[TourExportMapper::PAYLOAD_ORDER_ITEMS] = array_values($mergedOrderItems);

            $exportData[] = array_merge($tourData, [
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => $order[TourExportMapper::PAYLOAD_ORDER_REFERENCE],
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => $order[TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE],
                TourExportMapper::PAYLOAD_ORDER_ITEMS => $order[TourExportMapper::PAYLOAD_ORDER_ITEMS],
            ]);
        }

        return $exportData;
    }

    /**
     * @throws Exception
     * @return void
     */
    protected function setCurrentDate()
    {
        $this->currentDate = new DateTime('now');
        $projectTimezone = new DateTimeZone($this->tourConfig->getProjectTimeZone());

        $this
            ->currentDate
            ->setTimezone($projectTimezone);
    }

    /**
     * @param SpyBranch $branch
     * @return string
     */
    protected function getDurstGln(SpyBranch $branch): string
    {
        if($branch->getDurstGln() !== null){
            return $branch->getDurstGln();
        }

        return $this->tourConfig->getDurstIlnNumber();
    }

    /**
     * @param int $idBranch
     * @return string
     * @throws AmbiguousComparisonException
     */
    protected function getCurrentBillingReferenceForBranchById(int $idBranch) : string
    {
        $currentBillingPeriod = $this
            ->billingFacade
            ->getCurrentBillingPeriodForBranchById($idBranch);

        if($currentBillingPeriod !== null){
            return $currentBillingPeriod->getBillingReference();
        }

        return '';
    }

    /**
     * @return ObjectCollection
     */
    protected function findTourOrderItems(): ObjectCollection
    {
        $stateIds = $this->getStateIds();
        $processes = $this->getProcesses();

        $itemQuery = $this
            ->omsQueryContainer
            ->queryOrderItemsForConcreteTourInStateOrWithDeliveryStatus(
                array_keys($processes),
                $stateIds,
                $this->idTour
            );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $itemQuery->joinWithOrder();
        }

        return $itemQuery->find();
    }

    /**
     * @param ObjectCollection $items
     * @return ObjectCollection
     */
    protected function getTourOrders(ObjectCollection $items)
    {
        $orders = new ObjectCollection();

        foreach ($items as $item) {
            $order = $item->getOrder();

            if (!$orders->contains($order)) {
                $orders->append($order);
            }
        }

        return $orders;
    }
}
