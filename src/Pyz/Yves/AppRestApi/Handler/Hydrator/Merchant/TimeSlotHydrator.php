<?php
/**
 * Durst - project - TimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-24
 * Time: 12:47
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CartItemTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantTimeSlotKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\MerchantTimeSlotKeyResponseInterface;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlot;
use stdClass;

class TimeSlotHydrator implements HydratorInterface
{
    public const TIMESLOT_API_VERSION_2 = "v2";
    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var \Pyz\Client\Cart\CartClientInterface
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
     * TimeSlotHydrator constructor.
     *
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $client
     * @param \Pyz\Client\Cart\CartClientInterface $cartClient
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
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @param string $version
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $this
            ->getRequestedProductAmount($requestObject);

        $requestTransfer = (new AppApiRequestTransfer())
            ->setZipCode(
                $requestObject
                    ->{MerchantTimeSlotKeyRequestInterface::KEY_ZIP_CODE}
            )
            ->setBranchIds(
                [
                    $requestObject
                        ->{MerchantTimeSlotKeyRequestInterface::KEY_MERCHANT_ID},
                ]
            )
            ->setMaxSlots(
                $this
                    ->config
                    ->getTimeSlotMaxSlots()
            )
            ->setItemsPerSlot(
                $this
                    ->config
                    ->getTimeSlotMaxPerItem()
            )
            ->setRequestedProductsAmount(
                $this
                    ->productCount
            );


        if (isset($requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_USE_DAY_LIMIT})) {
            $requestTransfer
                ->setUseDayLimit(
                    $requestObject
                        ->{MerchantTimeSlotKeyRequestInterface::KEY_USE_DAY_LIMIT}
                );
        }

        foreach ($requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_CART} as $cartItem) {
            $cartTransfer = (new CartItemTransfer())
                ->setSku(
                    $cartItem
                        ->{MerchantTimeSlotKeyRequestInterface::KEY_CART_SKU}
                )
                ->setQuantity(
                    $cartItem
                        ->{MerchantTimeSlotKeyRequestInterface::KEY_CART_QUANTITY}
                );

            $requestTransfer
                ->addCartItems(
                    $cartTransfer
                );
        }

        $this->getRequestedProductsWeight(
            $requestTransfer
        );

        $requestTransfer
            ->setRequestedProductsWeight(
                $this
                    ->productWeight
            );

        $fetchFullyBookedTimeSlots = ($version === self::TIMESLOT_API_VERSION_2);

        $responseTransfer = $this
            ->client
            ->getPossibleTimeSlotsForBranches(
                $requestTransfer,
                $fetchFullyBookedTimeSlots
            );

        $this
            ->getRequestedProductsPrice(
                $requestTransfer
                    ->getCartItems(),
                $responseTransfer
                    ->getTimeSlots(),
                $requestObject
            );

        $responseObject
            ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOTS} = $this
            ->hydrateTimeSlots(
                $responseTransfer
                    ->getTimeSlots(),
                $version
            );
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return void
     */
    protected function getRequestedProductAmount(stdClass $requestObject): void
    {
        $this->productCount = 0;

        foreach ($requestObject->{MerchantTimeSlotKeyRequestInterface::KEY_CART} as $cartItem) {
            $this->productCount += $cartItem
                ->{MerchantTimeSlotKeyRequestInterface::KEY_CART_QUANTITY};
        }
    }

    /**
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function getRequestedProductsWeight(AppApiRequestTransfer $requestTransfer): void
    {
        $response = $this
            ->client
            ->getWeight(
                $requestTransfer
            );

        $this->productWeight = $response
            ->getRequestWeight();
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\CartItemTransfer[] $cartItemTransfers
     * @param \ArrayObject|\Generated\Shared\Transfer\ConcreteTimeSlotTransfer[] $concreteTimeSlots
     * @param \stdClass|null $requestObject
     * @return void
     */
    protected function getRequestedProductsPrice(
        ArrayObject $cartItemTransfers,
        ArrayObject $concreteTimeSlots,
        stdClass $requestObject = null
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

        $tmp = $concreteTimeSlots->getArrayCopy();
        $first = reset($tmp);
        /** @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $firstTimeSlot */

        $firstTimeSlot = $first;

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

        /** @var \Generated\Shared\Transfer\TotalsTransfer $totalsTransfer */
        $totalsTransfer = $cartChangeResponse
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->getTotals();

        $this->productPrice = ($totalsTransfer->getSubtotal() - $totalsTransfer->getDiscountTotal());
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ConcreteTimeSlotTransfer[] $concreteTimeSlotTransfers
     * @param string $version
     *
     * @return array
     */
    protected function hydrateTimeSlots(ArrayObject $concreteTimeSlotTransfers, string $version = "v1"): array
    {
        $timeSlotObjects = [];

        if ($concreteTimeSlotTransfers->count() <= 0) {
            return $timeSlotObjects;
        }

        /** @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer */
        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {
            if ($version != self::TIMESLOT_API_VERSION_2) {
                if ($this->hasEnoughPayloadLeft($concreteTimeSlotTransfer) !== true ||
                    $this->hasEnoughCustomersLeft($concreteTimeSlotTransfer) !== true
                ) {
                    continue;
                }
            }

            $minimumOrderValue = $this
                ->getMinimumOrderValue($concreteTimeSlotTransfer);

            $timeSlotObject = new stdClass();

            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_ID} = $concreteTimeSlotTransfer->getIdConcreteTimeSlot();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_MERCHANT_ID} = $concreteTimeSlotTransfer->getIdBranch();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_FROM} = $concreteTimeSlotTransfer->getStartTime();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_TO} = $concreteTimeSlotTransfer->getEndTime();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_TIME_SLOT_STRING} = $concreteTimeSlotTransfer->getFormattedString();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_CURRENCY} = '€';
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_USE_BRANCH_HP_KEY} = $this->getUseBranchSpecificKeyBasedOnStartDate($concreteTimeSlotTransfer);
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_TOTAL_DELIVERY_COST} = $concreteTimeSlotTransfer->getDeliveryCosts();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_START_RAW} = $concreteTimeSlotTransfer->getStartTimeRaw();
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_END_RAW} = $concreteTimeSlotTransfer->getEndTimeRaw();
            if ($version == self::TIMESLOT_API_VERSION_2) {
                if ($this->hasEnoughPayloadLeft($concreteTimeSlotTransfer) !== true) {
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_CODE} = ConcreteTimeSlot::ERROR_CODE_CONCRETE_TIME_SLOT_PAYLOAD_INVALID;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_VALIDITY} = false;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_MESSAGE} = ConcreteTimeSlot::ERROR_CONCRETE_TIME_SLOT_PAYLOAD_INVALID;
                } else if ($this->hasEnoughCustomersLeft($concreteTimeSlotTransfer) !== true) {
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_CODE} = ConcreteTimeSlot::ERROR_CODE_CONCRETE_TIME_SLOT_CUSTOMERS_INVALID;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_VALIDITY} = false;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_MESSAGE} = ConcreteTimeSlot::ERROR_CONCRETE_TIME_SLOT_CUSTOMERS_INVALID;
                } else if ($this->productPrice < $minimumOrderValue){
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_CODE} = ConcreteTimeSlot::ERROR_CODE_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_VALIDITY} = false;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_MESSAGE} = ConcreteTimeSlot::ERROR_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID;
                } else {
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_CODE} = ConcreteTimeSlot::SUCCESS_CODE;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_VALIDITY} = true;
                    $timeSlotObject
                        ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_MESSAGE} = null;
                }
            }

            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_TOTAL_MIN_VALUE} = max(
                    0,
                    ($minimumOrderValue - $this->productPrice)
                );
            $timeSlotObject
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_TOTAL_MIN_UNITS} = max(
                    0,
                    ($concreteTimeSlotTransfer->getMinUnits() - $this->productCount)
                );

            $timeSlotObjects[] = $timeSlotObject;
        }

        return $this
            ->sortConcreteTimeSlotsByStartDateRaw($timeSlotObjects);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return bool
     */
    protected function hasEnoughPayloadLeft(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): bool
    {
        return ($concreteTimeSlotTransfer->getRemainingPayload() >= $this->productWeight);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return bool
     */
    protected function hasEnoughCustomersLeft(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): bool
    {
        return ($concreteTimeSlotTransfer->getRemainingCustomer() > 0);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return bool
     */
    protected function hasEnoughProductsLeft(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): bool
    {
        return ($concreteTimeSlotTransfer->getRemainingProduct() >= $this->productCount);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     *
     * @return int
     */
    protected function getMinimumOrderValue(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): int
    {
        return $concreteTimeSlotTransfer->getMinValueFirst();
    }

    /**
     * @param \stdClass[] $concreteTimeSlotTransfers
     *
     * @return \stdClass[]
     */
    protected function sortConcreteTimeSlotsByStartDateRaw(array $concreteTimeSlotTransfers): array
    {
        usort($concreteTimeSlotTransfers, function ($firstConcreteTimeSlot, $secondConcreteTimeSlot) {
            $firstDeliveryDate = $firstConcreteTimeSlot
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_START_RAW};
            $secondDeliveryDate = $secondConcreteTimeSlot
                ->{MerchantTimeSlotKeyResponseInterface::KEY_TIME_SLOT_START_RAW};

            if ($firstDeliveryDate == $secondDeliveryDate) {
                return 0;
            } elseif ($firstDeliveryDate > $secondDeliveryDate) {
                return 1;
            }

            return -1;
        });

        return $concreteTimeSlotTransfers;
    }

    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
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
     * @param \Generated\Shared\Transfer\TimeSlotTransfer $timeSlotTransfer
     *
     * @return bool
     */
    protected function getUseBranchSpecificKeyBasedOnStartDate(ConcreteTimeSlotTransfer $timeSlotTransfer) : bool
    {
        if (date('Y-m-d', $timeSlotTransfer->getStartTimeRaw()) >= $this->getStartDateForBranchKeyUsageFromConfig()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getStartDateForBranchKeyUsageFromConfig() : string
    {
        return $this
            ->config
            ->getHeidelPayStartDateBranchSpecificKeys();
    }
}
