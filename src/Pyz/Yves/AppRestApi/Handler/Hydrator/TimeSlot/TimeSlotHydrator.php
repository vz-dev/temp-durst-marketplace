<?php
/**
 * Durst - project - TimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:10
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot;

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
use Pyz\Yves\AppRestApi\Handler\Json\Request\TimeSlotKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\TimeSlotKeyResponseInterface as Response;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class TimeSlotHydrator implements HydratorInterface
{
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
     * @var \Spryker\Yves\Money\Plugin\MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * TimeSlotHydrator constructor.
     *
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $client
     * @param \Pyz\Client\Cart\CartClientInterface $cartClient
     * @param \Spryker\Yves\Money\Plugin\MoneyPlugin $moneyPlugin
     */
    public function __construct(
        AppRestApiConfig $config,
        AppRestApiClientInterface $client,
        CartClientInterface $cartClient,
        MoneyPlugin $moneyPlugin
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->cartClient = $cartClient;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $requestTransfer = new AppApiRequestTransfer();
        $this->setBranchAndZip($requestObject, $requestTransfer);
        $this->setLimits($requestObject, $requestTransfer);
        $this->setCart($requestObject, $requestTransfer);
        $this->setRequestedProductsAmount($requestObject, $requestTransfer);
        $this->setRequestedProductsWeight($requestObject, $requestTransfer);

        $responseTransfer = $this
            ->client
            ->getPossibleTimeSlotsForBranches($requestTransfer);

        $items = $this->transformCartItemList($requestTransfer->getCartItems());
        $responseObject->{Response::KEY_TIME_SLOTS} = $this->hydrateTotals(
            $items,
            $responseTransfer->getTimeSlots(),
            $requestObject
        );
    }

    /**
     * @param \stdClass $requestObject
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function setBranchAndZip(stdClass $requestObject, AppApiRequestTransfer $requestTransfer)
    {
        $requestTransfer->setBranchIds($requestObject->{Request::KEY_MERCHANT_IDS});
        $requestTransfer->setZipCode($requestObject->{Request::KEY_ZIP_CODE});
    }

    /**
     * @param \stdClass $requestObject
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function setLimits(stdClass $requestObject, AppApiRequestTransfer $requestTransfer)
    {
        if (isset($requestObject->{Request::KEY_MAX_SLOTS}) && isset($requestObject->{Request::KEY_ITEMS_PER_SLOT})) {
            $requestTransfer->setMaxSlots($requestObject->{Request::KEY_MAX_SLOTS});
            $requestTransfer->setItemsPerSlot($requestObject->{Request::KEY_ITEMS_PER_SLOT});
            return;
        }

        $requestTransfer->setMaxSlots($this->config->getTimeSlotMaxSlots());
        $requestTransfer->setItemsPerSlot($this->config->getTimeSlotMaxPerItem());
    }

    /**
     * @param \stdClass $requestObject
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function setCart(stdClass $requestObject, AppApiRequestTransfer $requestTransfer)
    {
        foreach ($requestObject->{Request::KEY_CART} as $cartItem) {
            $transfer = (new CartItemTransfer())
                ->setSku($cartItem->{Request::KEY_CART_SKU})
                ->setQuantity($cartItem->{Request::KEY_CART_QUANTITY});
            $requestTransfer->addCartItems($transfer);
        }
    }

    /**
     * @param \stdClass $requestObject
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function setRequestedProductsAmount(stdClass $requestObject, AppApiRequestTransfer $requestTransfer)
    {
        $productsAmount = 0;
        foreach ($requestObject->{Request::KEY_CART} as $cartItem) {
            $productsAmount += $cartItem->{Request::KEY_CART_QUANTITY};
        }

        $requestTransfer->setRequestedProductsAmount($productsAmount);
    }

    /**
     * @param \stdClass $requestObject
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function setRequestedProductsWeight(stdClass $requestObject, AppApiRequestTransfer $requestTransfer)
    {
        $response = $this
            ->client
            ->getWeight($requestTransfer);

        $requestTransfer->setRequestedProductsWeight($response->getRequestWeight());
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $timeSlotTransfer
     * @param \stdClass $timeSlotObject
     *
     * @return \stdClass
     */
    protected function hydrateTimeSlot(ConcreteTimeSlotTransfer $timeSlotTransfer, stdClass $timeSlotObject): stdClass
    {
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_ID} = $timeSlotTransfer->getIdConcreteTimeSlot();
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_MERCHANT_ID} = $timeSlotTransfer->getIdBranch();
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_FROM} = $timeSlotTransfer->getStartTime();
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_TO} = $timeSlotTransfer->getEndTime();
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_TIME_SLOT_STRING} = $timeSlotTransfer->getFormattedString();
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_USE_BRANCH_HP_KEY} = $this->getUseBranchSpecificKeyBasedOnStartDate($timeSlotTransfer);
        $timeSlotObject
            ->{Response::KEY_TIME_SLOT_CURRENCY} = '€';

        return $timeSlotObject;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \ArrayObject|\Generated\Shared\Transfer\ConcreteTimeSlotTransfer[] $concreteTimeSlotTransfers
     * @param \stdClass|null $requestObject
     * @return array
     */
    protected function hydrateTotals(
        ArrayObject $items,
        ArrayObject $concreteTimeSlotTransfers,
        stdClass $requestObject = null
    ): array
    {
        $timeSlotObjects = [];

        if ($concreteTimeSlotTransfers->count() <= 0) {
            return $timeSlotObjects;
        }

        $concreteTimeSlotsArray = $this
            ->sortConcreteTimeSlotsByIdBranch($concreteTimeSlotTransfers);

        foreach ($concreteTimeSlotsArray as $idBranch => $concreteTimeSlots) {
            $branchTransfer = $this
                ->getBranchTransferById($idBranch);

            $cartChangeResponse = $this
                ->cartClient
                ->addItemsForBranchAndConcreteTimeSlots(
                    $items
                        ->getArrayCopy(),
                    $branchTransfer,
                    $concreteTimeSlots,
                    $requestObject
                );

            foreach ($cartChangeResponse->getConcreteTimeSlots() as $concreteTimeSlot) {
                $totalsTransfer = $concreteTimeSlot->getTotals();

                if ($totalsTransfer === null) {
                    continue;
                }

                $timeSlotObject = $this->hydrateTimeSlot($concreteTimeSlot, $this->createStdClass());

                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_CHEAPEST_SLOT} = false;
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_CART} = $this->formatMoney($totalsTransfer->getSubtotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_DELIVERY_COST} = $this->formatMoney($totalsTransfer->getDeliveryCostTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_DEPOSIT} = $this->formatMoney($totalsTransfer->getDepositTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_DISCOUNT} = $this->formatMoney($totalsTransfer->getDiscountTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_TAXES} = $this->formatMoney($totalsTransfer->getTaxTotal()->getAmount());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL} = $this->formatMoney($totalsTransfer->getGrandTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_MIN_VALUE} = $this->formatMoney($totalsTransfer->getMissingMinAmountTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_MIN_UNITS} = $totalsTransfer->getMissingMinUnitsTotal();
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_NET} = $this->formatMoney($totalsTransfer->getNetTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_EXPENSE} = $this->formatMoney($totalsTransfer->getExpenseTotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_GROSS_SUBTOTAL} = $this->formatMoney($totalsTransfer->getGrossSubtotal());
                $timeSlotObject
                    ->{Response::KEY_TIME_SLOT_TOTAL_DISPLAY} = $this->formatMoney($totalsTransfer->getDisplayTotal());

                $timeSlotObjects[] = $timeSlotObject;
            }
        }

        return $this
            ->sortConcreteTimeSlotsByStartDate($timeSlotObjects);
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
     * @param \ArrayObject $cartItemTransfers
     *
     * @return \ArrayObject
     */
    protected function transformCartItemList(ArrayObject $cartItemTransfers) : ArrayObject
    {
        $itemTransfers = new ArrayObject();
        foreach ($cartItemTransfers as $cartItemTransfer) {
            $itemTransfers->append($this->cartItemToItemTransfer($cartItemTransfer));
        }

        return $itemTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\CartItemTransfer $cartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function cartItemToItemTransfer(CartItemTransfer $cartItemTransfer) : ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku($cartItemTransfer->getSku())
            ->setQuantity($cartItemTransfer->getQuantity());
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ConcreteTimeSlotTransfer[] $concreteTimeSlotTransfers
     *
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer[]
     */
    protected function sortConcreteTimeSlotsByIdBranch(ArrayObject $concreteTimeSlotTransfers): array
    {
        $concreteTimeSlots = [];

        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {
            $concreteTimeSlots[$concreteTimeSlotTransfer->getIdBranch()][] = $concreteTimeSlotTransfer;
        }

        return $concreteTimeSlots;
    }

    /**
     * @param \stdClass[] $concreteTimeSlotTransfers
     *
     * @return \stdClass[]
     */
    protected function sortConcreteTimeSlotsByStartDate(array $concreteTimeSlotTransfers): array
    {
        usort($concreteTimeSlotTransfers, function ($firstConcreteTimeSlot, $secondConcreteTimeSlot) {
            $firstDeliveryDate = $firstConcreteTimeSlot->{Response::KEY_TIME_SLOT_FROM};
            $secondDeliveryDate = $secondConcreteTimeSlot->{Response::KEY_TIME_SLOT_FROM};

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
     * @param int $amount
     *
     * @return float
     */
    protected function formatMoney(int $amount)
    {
        return $this->moneyPlugin->convertIntegerToDecimal($amount);
    }

    /**
     * @return \stdClass
     */
    protected function createStdClass(): stdClass
    {
        return new stdClass();
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
