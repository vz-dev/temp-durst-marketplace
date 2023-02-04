<?php
/**
 * Durst - project - TimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 19:53
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Graphmasters;

use ArrayObject;
use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CartItemTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\GraphMastersApiTimeSlotResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\GraphmastersKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\GraphmastersKeyResponseInterface as Response;
use stdClass;

class TimeSlotHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var CartClientInterface
     */
    protected $cartClient;

    /**
     * @var int
     */
    protected $productCount = 0;

    /**
     * @var int
     */
    protected $productWeight = 0;

    /**
     * @var int
     */
    protected $productPrice = 0;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var null|array
     */
    protected $settings = null;

    /**
     * TimeSlotHydrator constructor.
     *
     * @param AppRestApiConfig $config
     * @param AppRestApiClientInterface $client
     * @param CartClientInterface $cartClient
     */
    public function __construct(
        AppRestApiConfig $config,
        AppRestApiClientInterface $client,
        CartClientInterface $cartClient
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->cartClient = $cartClient;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     * @param string $version
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        if (isset($requestObject->{Request::KEY_DEBUG}) && $requestObject->{Request::KEY_DEBUG} === true) {
            $this->debug = $requestObject->{Request::KEY_DEBUG};
        }

        $this
            ->getRequestedProductAmount($requestObject);

        $requestTransfer = (new AppApiRequestTransfer())
            ->setZipCode(
                $requestObject
                    ->{Request::KEY_ZIP_CODE}
            )
            ->setIdBranch(
                $requestObject
                        ->{Request::KEY_MERCHANT_ID}
            )
            ->setRequestedProductsAmount(
                $this
                    ->productCount
            )
            ->setShippingAddress(
                $this->hydrateShippingAddress($requestObject->{Request::KEY_SHIPPING_ADDRESS})
            );

        foreach ($requestObject->{Request::KEY_CART} as $cartItem) {
            $cartTransfer = (new CartItemTransfer())
                ->setSku(
                    $cartItem
                        ->{Request::KEY_CART_SKU}
                )
                ->setQuantity(
                    $cartItem
                        ->{Request::KEY_CART_QUANTITY}
                );

            $requestTransfer
                ->addCartItems(
                    $cartTransfer
                );
        }

        $requestTransfer
            ->setRequestedProductsWeight(
                $this->getRequestedProductsWeight($requestTransfer, $requestObject)
            );

        $this->settings = $this
            ->client
            ->getGMSettings($requestTransfer->getIdBranch());

        $requestTransfer
            ->setGraphmasterSettings($this->settings);

        $this
            ->client
            ->evaluateGMTimeSlots($requestTransfer);

        $responseTransfer = $this
            ->client
            ->evaluateTimeSlots($requestTransfer);

        $this
            ->getRequestedProductsPrice(
                $requestTransfer
                    ->getCartItems(),
                $responseTransfer
                    ->getTimeSlots(),
                $requestObject
            );

        if ($responseTransfer->getError() !== null) {
            $requestObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_ERROR_CODE} = $responseTransfer->getError()->getCode();
            $requestObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_ERROR} = $responseTransfer->getError()->getMessage();

            return;
        }

        $responseObject
            ->{Response::KEY_TIME_SLOTS} = $this
            ->hydrateTimeSlots(
                $responseTransfer
                    ->getGraphMastersEvaluatedTimeSlots(),
                $requestObject
                    ->{Request::KEY_ZIP_CODE}
            );
    }

    /**
     * @param stdClass $requestObject
     *
     * @return void
     */
    protected function getRequestedProductAmount(stdClass $requestObject): void
    {
        $this->productCount = 0;

        foreach ($requestObject->{Request::KEY_CART} as $cartItem) {
            $this->productCount += $cartItem
                ->{Request::KEY_CART_QUANTITY};
        }
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @param stdClass $requestObject
     *
     * @return int
     */
    protected function getRequestedProductsWeight(AppApiRequestTransfer $requestTransfer, stdClass $requestObject): int
    {
        if ($requestObject->{Request::KEY_WEIGHT} !== null && $requestObject->{Request::KEY_WEIGHT} !== 0) {
            return $requestObject->{Request::KEY_WEIGHT};
        }

        $response = $this
            ->client
            ->getWeight(
                $requestTransfer
            );

        return $response->getRequestWeight();
    }

    /**
     * @param ArrayObject|CartItemTransfer[] $cartItemTransfers
     * @param ArrayObject|ConcreteTimeSlotTransfer[] $concreteTimeSlots
     * @param stdClass|null $requestObject
     *
     * @return void
     */
    protected function getRequestedProductsPrice(
        ArrayObject $cartItemTransfers,
        ArrayObject $concreteTimeSlots,
        ?stdClass $requestObject = null
    ): void {
        $this->productPrice = 0;

        if ($concreteTimeSlots->count() <= 0) {
            return;
        }

        $itemTransfers = new ArrayObject();

        foreach ($cartItemTransfers as $cartItemTransfer) {
            $itemTransfer = (new ItemTransfer())
                ->setSku($cartItemTransfer->getSku())
                ->setQuantity($cartItemTransfer->getQuantity());
            $itemTransfers
                ->append($itemTransfer);
        }

        /** @var ConcreteTimeSlotTransfer $firstTimeSlot */
        $firstTimeSlot = $concreteTimeSlots
            ->offsetGet(0);

        $branchTransfer = $this
            ->getBranchTransferById(
                $firstTimeSlot
                    ->getIdBranch()
            );

        $cartChangeResponse = $this
            ->cartClient
            ->addItemsForBranchAndConcreteTimeSlot(
                $itemTransfers
                    ->getArrayCopy(),
                $branchTransfer,
                $firstTimeSlot,
                $requestObject
            );

        /** @var TotalsTransfer $totalsTransfer */
        $totalsTransfer = $cartChangeResponse
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->getTotals();

        $this->productPrice = ($totalsTransfer->getSubtotal() - $totalsTransfer->getDiscountTotal());
    }

    /**
     * @param ArrayObject $timeSlotTransfers
     *
     * @return array
     */
    protected function hydrateTimeSlots(ArrayObject $timeSlotTransfers, string $zipCode): array
    {
        $timeSlotObjects = [];
        $deliveryArea = $this->getDeliveryAreaByZip($zipCode);

        /** @var GraphMastersApiTimeSlotResponseTransfer $timeSlotTransfer */
        foreach ($timeSlotTransfers as $timeSlotTransfer) {
            if ($timeSlotTransfer->getTimeSlotPossible() === false && $this->debug !== true) {
                continue;
            }

            if ($timeSlotTransfer->getEta() !== null) {
                $timeSlotTransfer->setEta(preg_replace('/\.[0-9]{9}/', '', $timeSlotTransfer->getEta()));
            }

            if ($timeSlotTransfer->getEta() === null) {
                $timeSlotTransfer->setEta($this->getMiddleOfTimeSlot($timeSlotTransfer->getStartTime(), $timeSlotTransfer->getEndTime()));
            }

            $timeSlotObject = new stdClass();

            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_START_TIME} = $timeSlotTransfer->getStartTime();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_END_TIME} = $timeSlotTransfer->getEndTime();

            if ($timeSlotTransfer->getReason() === 'small') {
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_START_TIME} = $this->getDecreasedSlot($timeSlotTransfer->getStartTime(), $timeSlotTransfer->getEta(), $timeSlotTransfer->getCostInExtraDrivingMinutes(), $deliveryArea);
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_END_TIME} = $this->getIncreasedSlot($timeSlotTransfer->getEndTime(), $timeSlotTransfer->getEta(), $timeSlotTransfer->getCostInExtraDrivingMinutes(), $deliveryArea);
            }

            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_IMPORTANCE} = $timeSlotTransfer->getImportance();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_REASON} = $timeSlotTransfer->getReason();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_EVALUATION_SUCCEEDED} = $timeSlotTransfer->getEvaluationSucceeded();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_TIME_SLOT_POSSIBLE} = $timeSlotTransfer->getTimeSlotPossible();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_EXTRA_COST_DRIVING} = $timeSlotTransfer->getCostInExtraDrivingMinutes();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_EXTRA_WORK_TIME_MINS} = $timeSlotTransfer->getExtraWorkTimeMinutes();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_EXTRA_DISTANCE_KILOMETERS} = $timeSlotTransfer->getExtraDistanceKilometer();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_ETA} = $timeSlotTransfer->getEta();
            $timeSlotObject
                ->{Response::EVALUATE_TIME_SLOTS_KEY_ERROR} = $timeSlotTransfer->getError();

            if ($this->debug === true) {
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_ORIG_START_TIME} = $timeSlotTransfer->getStartTime();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_ORIG_END_TIME} = $timeSlotTransfer->getEndTime();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_TOUR_ID} = $timeSlotTransfer->getTourId();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_DRIVER_ID} = $timeSlotTransfer->getDriverId();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_VEHICLE_ID} = $timeSlotTransfer->getVehicleId();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_NUM_OF_ORDERS} = $timeSlotTransfer->getNumberOfActualOrders();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_NUM_OF_PREDICTED_ORDERS} = $timeSlotTransfer->getNumberOfPredictedOrders();
                $timeSlotObject
                    ->{Response::EVALUATE_TIME_SLOTS_KEY_NUM_UNPERFROMED_ORDERS} = $timeSlotTransfer->getNumberOfPredictedOrders();
            }

            $timeSlotObjects[] = $timeSlotObject;
        }

        return $timeSlotObjects;
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return int
     */
    protected function getMinimumOrderValue(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): int
    {
        if ($concreteTimeSlotTransfer->getMaxCustomer() > $concreteTimeSlotTransfer->getRemainingCustomer()) {
            return $concreteTimeSlotTransfer
                ->getMinValueFollowing();
        }

        return $concreteTimeSlotTransfer
            ->getMinValueFirst();
    }

    /**
     * @param int $idBranch
     *
     * @return BranchTransfer
     */
    protected function getBranchTransferById(int $idBranch): BranchTransfer
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($idBranch);

        return $this
            ->client
            ->getBranchById($requestTransfer);
    }

    /**
     * @param $address
     *
     * @return AddressTransfer
     */
    protected function hydrateAddressTransfer($address) : AddressTransfer
    {
        $addressTransfer = (new AddressTransfer())
            ->setFirstName(trim($address->{Request::KEY_ADDRESS_FIRST_NAME}))
            ->setLastName(trim($address->{Request::KEY_ADDRESS_LAST_NAME}))
            ->setSalutation(trim($address->{Request::KEY_ADDRESS_SALUTATION}))
            ->setAddress1(trim($address->{Request::KEY_ADDRESS_ADDRESS_1}))
            ->setAddress2(trim($address->{Request::KEY_ADDRESS_ADDRESS_2}))
            ->setAddress3(trim($address->{Request::KEY_ADDRESS_ADDRESS_3}))
            ->setZipCode(trim($address->{Request::KEY_ADDRESS_ZIP_CODE}))
            ->setCity(trim($address->{Request::KEY_ADDRESS_CITY}))
            ->setCompany(trim($address->{Request::KEY_ADDRESS_COMPANY}))
            ->setIso2Code('DE')
            ->setPhone(trim($address->{Request::KEY_ADDRESS_PHONE}));

        return $addressTransfer;
    }

    /**
     * @param stdClass $address
     *
     * @return AddressTransfer
     */
    protected function hydrateShippingAddress(stdClass $address) : AddressTransfer
    {
        $addressTransfer = $this->hydrateAddressTransfer($address);

        if (property_exists($address, Request::KEY_ADDRESS_LAT) === true) {
            $addressTransfer->setLat($address->{Request::KEY_ADDRESS_LAT});
        }
        if (property_exists($address, Request::KEY_ADDRESS_LNG) === true) {
            $addressTransfer->setLng($address->{Request::KEY_ADDRESS_LNG});
        }
        if (property_exists($address, Request::KEY_ADDRESS_FLOOR) === true) {
            $addressTransfer->setFloor($address->{Request::KEY_ADDRESS_FLOOR});
        }
        if (property_exists($address, Request::KEY_ADDRESS_ELEVATOR) === true) {
            $addressTransfer->setElevator($address->{Request::KEY_ADDRESS_ELEVATOR});
        }

        return $addressTransfer;
    }

    /**
     * @param string $startTime
     * @param string|null $etaTime
     * @param float|int $edtm
     * @param array|null $deliveryArea
     *
     * @return string
     */
    protected function getDecreasedSlot(string $startTime, ?string $etaTime, float $edtm, ?array $deliveryArea) : string
    {
        if ($etaTime === null) {
            return $startTime;
        }

        $dt = $this->roundToNearestMinuteInterval(new Datetime($etaTime));
        $interval = new DateInterval($this->getTimeIntervalString($deliveryArea, $edtm));
        $dt->sub($interval);

        return $dt->format(DATE_ATOM);
    }

    /**
     * @param string $endTime
     * @param string|null $etaTime
     * @param array|null|float $deliveryArea
     *
     * @return string
     */
    protected function getIncreasedSlot(string $endTime, ?string $etaTime, float $edtm, ?array $deliveryArea) : string
    {
        if ($etaTime === null) {
            return $endTime;
        }

        $dt = $this->roundToNearestMinuteInterval(new Datetime($etaTime));
        $interval = new DateInterval($this->getTimeIntervalString($deliveryArea, $edtm));
        $dt->add($interval);

        return $dt->format(DATE_ATOM);
    }

    /**
     * @param array $deliveryArea
     * @param float $edtm
     *
     * @return string
     */
    protected function getTimeIntervalString(array $deliveryArea, float $edtm) : string
    {
        $hours = 1;

        if ($deliveryArea['slot_size'] !== null) {
            $hours = $deliveryArea['slot_size'];
        } else {
            switch ($edtm) {
                case $edtm < $deliveryArea['edtm_cutoff_small']:
                    $hours = 1;
                    break;
                case $edtm < $deliveryArea['edtm_cutoff_medium']:
                    $hours = 1.5;
                    break;
                case $edtm < $deliveryArea['edtm_cutoff_large']:
                    $hours = 2;
                    break;
                case $edtm > $deliveryArea['edtm_cutoff_xlarge']:
                    $hours = 4;
                    break;
                default:
                    $hours = 2;
                    break;
            }
        }

        $mins = $hours * 60 / 2;

        return sprintf(
            'PT%sM',
            $mins
        );
    }

    /**
     * @param DateTime $dateTime
     * @param int $minuteInterval
     *
     * @return DateTime|false
     */
    protected function roundToNearestMinuteInterval(DateTime $dateTime, int $minuteInterval = 5)
    {
        return $dateTime->setTime(
            $dateTime->format('H'),
            round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
            0
        );
    }

    /**
     * @param string $startTime
     * @param string $endTime
     *
     * @return string
     */
    protected function getMiddleOfTimeSlot(string $startTime, string $endTime) : string
    {
        $midPoint = (strtotime($startTime) + strtotime($endTime)) / 2;
        $dt = new DateTime('@' . $midPoint);
        $dt->setTimezone(new DateTimeZone('Europe/Berlin'));

        return $dt->format('c');
    }

    /**
     * @param string $zipCode
     *
     * @return array|null
     */
    protected function getDeliveryAreaByZip(string $zipCode) : ?array
    {
        foreach ($this->settings['delivery_areas'] as $deliveryArea) {
            if (in_array($zipCode, $deliveryArea['zip_codes'])) {
                return $deliveryArea;
            }
        }

        return null;
    }
}
