<?php
/**
 * Durst - project - TourDriverAppMapper.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 10.06.20
 * Time: 15:57
 */

namespace Pyz\Zed\Tour\Business\Mapper;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\DriverAppCustomerTransfer;
use Generated\Shared\Transfer\DriverAppOrderCommentTransfer;
use Generated\Shared\Transfer\DriverAppOrderDiscountTransfer;
use Generated\Shared\Transfer\DriverAppOrderItemTransfer;
use Generated\Shared\Transfer\DriverAppOrderTransfer;
use Generated\Shared\Transfer\DriverAppShippingAddressTransfer;
use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Sales\Persistence\SpySalesDiscount;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstVehicleCategoryTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\OrderItemStateException;
use Pyz\Zed\Tour\Business\Mapper\Product\ProductRepositoryInterface;
use Pyz\Zed\Tour\TourConfig;

class TourDriverAppMapper implements TourDriverappMapperInterface
{
    protected const INTEGRA_SKU_PREFIX = 'integra';

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * @var array
     */
    protected $paymentMethods;

    /**
     * TourDriverAppMapper constructor.
     * @param MerchantFacadeInterface $merchantFacade
     * @param ProductRepositoryInterface $productRepository
     * @param TourConfig $tourConfig
     */
    public function __construct(
        MerchantFacadeInterface    $merchantFacade,
        ProductRepositoryInterface $productRepository,
        TourConfig                 $tourConfig
    ) {
        $this->merchantFacade = $merchantFacade;
        $this->productRepository = $productRepository;
        $this->tourConfig = $tourConfig;
    }

    /**
     * {@inheritDoc}
     *
     * @param iterable $concreteTourEntities
     * @param array $skus
     * @param DriverTransfer $driverTransfer
     * @param array $stateWhitelist
     * @return array
     */
    public function mapEagerLoadedTourEntitiesToTransfers(iterable $concreteTourEntities, array $skus, DriverTransfer $driverTransfer, array $stateWhitelist): array
    {
        $this->productRepository->loadProductsBySkus($skus);

        $driverAppTours = [];
        foreach ($concreteTourEntities as $concreteTourEntity) {
            if ($concreteTourEntity->getFkBranch() !== $driverTransfer->getFkBranch()) {
                continue;
            }
            $driverAppTour = $this->mapTour($concreteTourEntity, $driverTransfer, $stateWhitelist);
            if ($driverAppTour->getOrders()->count() > 0) {
                $driverAppTours[] = $driverAppTour;
            }
        }

        return $driverAppTours;
    }

    /**
     * {@inheritDoc}
     *
     * @param iterable|DstGraphmastersTour[] $graphmastersTourEntities
     * @param array $skus the skus of all contained order items so they can be batch loaded
     * @param DriverTransfer $driverTransfer
     * @param array $stateWhitelist
     * @return array|DriverAppTourTransfer[]
     */
    public function mapEagerLoadedGraphmastersTourEntitiesToTransfers(
        iterable $graphmastersTourEntities,
        array $skus,
        DriverTransfer $driverTransfer,
        array $stateWhitelist
    ): array {
        $this->productRepository->loadProductsBySkus($skus);

        $driverAppTours = [];

        foreach ($graphmastersTourEntities as $graphmastersTourEntity) {
            if ($graphmastersTourEntity->getFkBranch() !== $driverTransfer->getFkBranch()) {
                continue;
            }

            $driverAppTour = $this->mapGraphmastersTour($graphmastersTourEntity, $driverTransfer, $stateWhitelist);

            if ($driverAppTour->getOrders()->count() > 0) {
                $driverAppTours[] = $driverAppTour;
            }
        }

        return $driverAppTours;
    }

    /**
     * @param DstConcreteTour $concreteTour
     *
     * @return DriverAppTourTransfer
     */
    protected function mapTour(DstConcreteTour $concreteTour, DriverTransfer $driverTransfer, array $stateWhitelist): DriverAppTourTransfer
    {
        $driverTour = (new DriverAppTourTransfer())
            ->setIdTour($concreteTour->getIdConcreteTour())
            ->setTourDate($concreteTour->getDate()->format('Y-m-d'))
            ->setTourStart(sprintf('%d', $this->getTourStart($concreteTour)->getTimestamp()))
            ->setTourEnd(sprintf('%d', $this->getTourEnd($concreteTour)->getTimestamp()))
            ->setComment($concreteTour->getComment())
            ->setTravelMode($this->getTravelModeIfVehicleIsSet($concreteTour))
            ->setWarehouseLat((string) $concreteTour->getSpyBranch()->getWarehouseLat())
            ->setWarehouseLng((string) $concreteTour->getSpyBranch()->getWarehouseLng())
            ->setTourReference($concreteTour->getTourReference());

        $driverOrders = [];
        $deliveryAreas = [];
        foreach ($concreteTour->getSpyConcreteTimeSlots() as $concreteTimeSlot) {
            $timeSlotStart = $concreteTimeSlot->getStartTime()->getTimestamp();
            $timeSlotEnd = $concreteTimeSlot->getEndTime()->getTimestamp();

            foreach ($concreteTimeSlot->getSpySalesOrders() as $order) {
                if ($order->getFkBranch() !== $driverTransfer->getFkBranch()) {
                    continue;
                }
                try {
                    $driverOrder = $this->mapOrder(
                        $order,
                        $timeSlotStart,
                        $timeSlotEnd,
                        $stateWhitelist
                    );
                    $driverOrders[$driverOrder->getIdOrder()] = $driverOrder;
                    $deliveryAreas[] = $order
                        ->getShippingAddress()
                        ->getZipCode();
                } catch (OrderItemStateException $e) {
                    // this removed orders that don't match the configured accepted state.
                    // Propel does not map these entities correct
                }
            }
        }

        $driverTour->setOrders(new ArrayObject(array_values($driverOrders)));

        $driverTour
            ->setDeliveryAreas(
                array_values(array_unique($deliveryAreas))
            );

        return $driverTour;
    }

    /**
     * @param DstGraphmastersTour $graphmastersTour
     * @param DriverTransfer $driverTransfer
     * @param array $stateWhitelist
     *
     * @return DriverAppTourTransfer
     *
     * @throws PropelException
     */
    protected function mapGraphmastersTour(
        DstGraphmastersTour $graphmastersTour,
        DriverTransfer $driverTransfer,
        array $stateWhitelist
    ): DriverAppTourTransfer {
        $driverTour = (new DriverAppTourTransfer())
            ->setIdTour($graphmastersTour->getIdGraphmastersTour())
            ->setTourDate($graphmastersTour->getDate()->format('Y-m-d'))
            ->setTourStart(sprintf('%d', $graphmastersTour->getTourStartEta()->getTimestamp()))
            ->setTourEnd(sprintf('%d', $graphmastersTour->getTourDestinationEta()->getTimestamp()))
            ->setComment($graphmastersTour->getComment())
            ->setTravelMode(DstVehicleCategoryTableMap::COL_PROFILE_CAR) // @TODO: Enable setting other travel modes
            ->setWarehouseLat((string) $graphmastersTour->getSpyBranch()->getWarehouseLat())
            ->setWarehouseLng((string) $graphmastersTour->getSpyBranch()->getWarehouseLng())
            ->setTourReference($graphmastersTour->getReference());

        $driverOrders = [];
        $deliveryAreas = [];

        foreach ($graphmastersTour->getDstGraphmastersOrders() as $graphmastersOrder) {
            $order = $graphmastersOrder->getSpySalesOrder();

            if ($order->getFkBranch() !== $driverTransfer->getFkBranch()  || $order->getGmStartTime() === null) {
                continue;
            }

            // Add Delivery Order from GM Order to Order entity
            $order->setDeliveryOrder($graphmastersOrder->getDeliveryOrder());

            try {
                $driverOrder = $this->mapOrder(
                    $order,
                    $order->getGmStartTime()->getTimestamp(),
                    $order->getGmEndTime()->getTimestamp(),
                    $stateWhitelist
                );
                $driverOrders[$driverOrder->getIdOrder()] = $driverOrder;
                $deliveryAreas[] = $order
                    ->getShippingAddress()
                    ->getZipCode();
            } catch (OrderItemStateException $e) {
                // this removed orders that don't match the configured accepted state.
                // Propel does not map these entities correct
            }
        }

        $driverTour->setOrders(new ArrayObject(array_values($driverOrders)));

        $driverTour
            ->setDeliveryAreas(
                array_values(array_unique($deliveryAreas))
            );

        return $driverTour;
    }

    /**
     * @param SpySalesOrder $order
     * @param int $timeSlotStart
     * @param int $timeSlotEnd
     * @param int[] $stateWhitelist
     *
     * @return DriverAppOrderTransfer
     * @throws ArrayObject
     * @throws PropelException
     *
     */
    protected function mapOrder(
        SpySalesOrder $order,
        int $timeSlotStart,
        int $timeSlotEnd,
        array $stateWhitelist
    ): DriverAppOrderTransfer {
        $orderTransfer = (new DriverAppOrderTransfer())
            ->setTimeSlotFrom($timeSlotStart)
            ->setTimeSlotTo($timeSlotEnd)
            ->setIdOrder($order->getIdSalesOrder())
            ->setOrderReference($order->getOrderReference())
            ->setIsExternal($order->getIsExternal())
            ->setIsPrivate($order->getIsPrivate())
            ->setCustomer($this->mapCustomer($order))
            ->setCustomerNote('')
            ->setPaymentMethod($this->getPaymentMethod($order))
            ->setPaymentCode($this->getPaymentMethodCode($order))
            ->setDeliveryOrder($order->getDeliveryOrder());

        foreach ($order->getOrderComments() as $orderComment) {
            $orderTransfer->addComments(
                (new DriverAppOrderCommentTransfer())
                    ->setType($orderComment->getType())
                    ->setMessage($orderComment->getMessage())
            );
        }

        $driverItems = [];
        foreach ($order->getItems() as $item) {
            if (in_array($item->getFkOmsOrderItemState(), $stateWhitelist) !== true) {
                throw OrderItemStateException::build($item->getFkOmsOrderItemState());
            }
            $driverItem = $this->mapOrderItem($item);
            $driverItems[$driverItem->getIdOrderItem()] = $driverItem;
        }
        $orderTransfer->setOrderItems(new ArrayObject(array_values($driverItems)));

        $orderTransfer->setShippingAddress($this->mapShippingAddress($order));

        if ($order->hasDiscount()) {
            foreach ($order->getCachedDiscounts() as $orderDiscount) {
                $orderTransfer->addDiscounts($this->mapOrderDiscount($orderDiscount));
            }
        }

        return $orderTransfer;
    }

    /**
     * @param SpySalesOrder $order
     *
     * @return string
     */
    protected function getPaymentMethod(SpySalesOrder $order): string
    {
        if($order->isExternal() === true && $order->getIntegraPaymentMethod() !== null && array_key_exists($order->getIntegraPaymentMethod(), $this->tourConfig->getIntegraGbzPaymentMethods())){
            return $this->tourConfig->getIntegraGbzPaymentMethods()[$order->getIntegraPaymentMethod()];
        }

        return $this
            ->getPaymentMethodNameByCode(
                $this->getPaymentMethodCode($order)
            );
    }

    /**
     * @param SpySalesOrder $order
     *
     * @return string
     */
    protected function getPaymentMethodCode(SpySalesOrder $order): string
    {
        if($order->isExternal() === true && $order->getIntegraPaymentMethod() !== null && array_key_exists($order->getIntegraPaymentMethod(), $this->tourConfig->getIntegraGbzPaymentCodes())){
            return $this->tourConfig->getIntegraGbzPaymentCodes()[$order->getIntegraPaymentMethod()];
        }

        /** @var SpySalesPayment $payment */
        $payment = $order->getOrders()->getFirst();
        if ($payment === null) {
            return '';
        }

        return $payment->getSalesPaymentMethodType()->getPaymentMethod();
    }

    /**
     * @param SpySalesOrder $order
     *
     * @return DriverAppShippingAddressTransfer
     */
    protected function mapShippingAddress(SpySalesOrder $order): DriverAppShippingAddressTransfer
    {
        $address = $order->getShippingAddress();
        if ($address === null) {
            return new DriverAppShippingAddressTransfer();
        }
        return (new DriverAppShippingAddressTransfer())
            ->setSalutation($address->getSalutation())
            ->setPhone($address->getPhone())
            ->setCompany($this->replaceNullValues($address->getCompany()))
            ->setFirstName($address->getFirstName())
            ->setLastName($address->getLastName())
            ->setAddress1($address->getAddress1())
            ->setAddress2($address->getAddress2())
            ->setAddress3($address->getAddress3())
            ->setZipCode($address->getZipCode())
            ->setCity($address->getCity())
            ->setComment($address->getComment())
            ->setFloor($address->getFloor())
            ->setElevator($address->getElevator());
    }

    /**
     * @param SpySalesOrderItem $item
     *
     * @return DriverAppOrderItemTransfer
     */
    protected function mapOrderItem(SpySalesOrderItem $item): DriverAppOrderItemTransfer
    {
        return (new DriverAppOrderItemTransfer())
            ->setQuantity($item->getQuantity())
            ->setIdOrderItem($item->getIdSalesOrderItem())
            ->setSku($item->getSku())
            ->setPriceSingle($this->getSinglePrice($item->getPriceToPayAggregation(), $item->getQuantity()))
            ->setPriceTotal($item->getPriceToPayAggregation())
            ->setTaxRate($item->getTaxRate())
            ->setTaxAmount($item->getTaxAmountFullAggregation())
            ->setDepositSingle($item->getDepositAmount())
            ->setGtin($this->productRepository->getGtinsBySku($item->getSku()))
            ->setProductName($this->getNameFromItemOrProduct($item))
            ->setUnitName($this->getUnitNameOrUnknown($item));
    }

    /**
     * @param int $totalPrice
     * @param int $quantity
     *
     * @return int
     */
    protected function getSinglePrice(int $totalPrice, int $quantity): int
    {
        if ($quantity < 2) {
            return $totalPrice;
        }

        return $totalPrice / $quantity;
    }

    /**
     * @param SpySalesOrder $order
     *
     * @return DriverAppCustomerTransfer
     */
    protected function mapCustomer(SpySalesOrder $order): DriverAppCustomerTransfer
    {
        $billingAddress = $order->getBillingAddress();
        if ($billingAddress === null) {
            return new DriverAppCustomerTransfer();
        }
        return (new DriverAppCustomerTransfer())
            ->setFirstName($billingAddress->getFirstName())
            ->setLastName($billingAddress->getLastName())
            ->setCompany($this->replaceNullValues($billingAddress->getCompany()))
            ->setEmail($order->getEmail())
            ->setPhone($billingAddress->getPhone())
            ->setSalutation($billingAddress->getSalutation());
    }

    /**
     * @param DstConcreteTour $concreteTour
     *
     * @return DateTime
     */
    protected function getTourStart(DstConcreteTour $concreteTour): DateTime
    {
        $start = null;
        foreach ($concreteTour->getSpyConcreteTimeSlots() as $concreteTimeSlot) {
            if ($start === null || $concreteTimeSlot->getStartTime() < $start->getStartTime()) {
                $start = $concreteTimeSlot;
            }
        }

        return $start->getStartTime();
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function getPaymentMethodNameByCode(string $code): string
    {
        if ($this->paymentMethods === null) {
            $this->loadPaymentMethods();
        }

        if (array_key_exists($code, $this->paymentMethods) !== true) {
            return '-';
        }

        return $this->paymentMethods[$code];
    }

    /**
     * @return void
     */
    protected function loadPaymentMethods(): void
    {
        $paymentMethodTransfers = $this
            ->merchantFacade
            ->getPaymentMethods();

        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            $this->paymentMethods[$paymentMethodTransfer->getCode()] = $paymentMethodTransfer->getName();
        }
    }

    /**
     * @param DstConcreteTour $concreteTour
     *
     * @return DateTime
     */
    protected function getTourEnd(DstConcreteTour $concreteTour): DateTime
    {
        $end = null;
        foreach ($concreteTour->getSpyConcreteTimeSlots() as $concreteTimeSlot) {
            if ($end === null || $concreteTimeSlot->getEndTime() > $end->getEndTime()) {
                $end = $concreteTimeSlot;
            }
        }

        return $end->getEndTime();
    }

    /**
     * @param SpySalesOrderItem $item
     * @return string
     */
    protected function getNameFromItemOrProduct(SpySalesOrderItem $item) : string
    {
        if($this->isIntegraSku($item->getSku()) === true)
        {
            return trim($item->getName());
        }

        return $this->productRepository->getProductNameBySku($item->getSku());
    }

    /**
     * @param SpySalesOrderItem $item
     * @return string
     */
    protected function getUnitNameOrUnknown(SpySalesOrderItem $item) : string
    {
        if($this->isIntegraSku($item->getSku()) === true)
        {
            return 'unknown';
        }

        return $this->productRepository->getProductUnitBySku($item->getSku());
    }

    /**
     * @param string $sku
     * @return bool
     */
    protected function isIntegraSku(string $sku) : bool
    {
        return (substr($sku, 0, strlen(static::INTEGRA_SKU_PREFIX)) === static::INTEGRA_SKU_PREFIX);
    }

    /**
     * @param string|null $value
     * @return string
     */
    protected function replaceNullValues(?string $value) : string
    {
        if($value === null){
            return "";
        }

        return $value;
    }

    /**
     * @param SpySalesDiscount $orderDiscount
     *
     * @return DriverAppOrderDiscountTransfer
     *
     * @throws PropelException
     */
    protected function mapOrderDiscount(SpySalesDiscount $orderDiscount): DriverAppOrderDiscountTransfer
    {
        $orderDiscountTransfer = (new DriverAppOrderDiscountTransfer())
            ->setId($orderDiscount->getIdSalesDiscount())
            ->setName($orderDiscount->getDiscountName())
            ->setAmount($orderDiscount->getAmount() / 100);

        if ($orderDiscount->getExpense() !== null) {
            $orderDiscountTransfer->setExpenseType($orderDiscount->getExpense()->getType());
        }

        if ($orderDiscount->getSpyDiscount()->getDecisionRuleQueryString() !== null) {
            $minSubTotal = $this->getDiscountMinSubTotal($orderDiscount);

            if ($minSubTotal !== null) {
                $orderDiscountTransfer->setMinSubTotal($minSubTotal);
            }
        }

        return $orderDiscountTransfer;
    }

    /**
     * @param SpySalesDiscount $orderDiscount
     *
     * @return float|null
     *
     * @throws PropelException
     */
    protected function getDiscountMinSubTotal(SpySalesDiscount $orderDiscount): ?float
    {
        $decisionRuleQuery = $orderDiscount->getSpyDiscount()->getDecisionRuleQueryString();

        $matches = null;
        preg_match('/sub-total >= \'([0-9,]+)\'/', $decisionRuleQuery, $matches);

        $minSubTotal = isset($matches[1]) ? (float) str_replace(',', '.', $matches[1]) : null;

        return $minSubTotal;
    }

    /**
     * @param DstConcreteTour $concreteTour
     * @return string|null
     * @throws PropelException
     */
    protected function getTravelModeIfVehicleIsSet(DstConcreteTour $concreteTour) : ?string
    {
        if($concreteTour->getDstAbstractTour()->getDstVehicleType() === null)
        {
            return null;
        }

        return $concreteTour->getDstAbstractTour()->getDstVehicleType()->getDstVehicleCategory()->getProfile();
    }
}
