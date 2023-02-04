<?php
/**
 * Durst - project - ExpenseHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-06
 * Time: 10:35
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Overview;


use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OverviewKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\OverviewKeyResponseInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class ExpenseHydrator implements HydratorInterface
{
    public const PRODUCT_NAME_ATTRIBUTE = 'name';
    public const UNIT_NAME_ATTRIBUTE = 'unit';

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
     * @var MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * ExpenseHydrator constructor.
     * @param AppRestApiConfig $config
     * @param AppRestApiClientInterface $client
     * @param CartClientInterface $cartClient
     * @param MoneyPlugin $moneyPlugin
     */
    public function __construct(
        AppRestApiConfig $config,
        AppRestApiClientInterface $client,
        CartClientInterface $cartClient,
        MoneyPlugin $moneyPlugin)
    {
        $this->config = $config;
        $this->client = $client;
        $this->cartClient = $cartClient;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $idMerchant = $this->getIdBranch($requestObject, $responseObject, $version);

        $idTimeSlot = $requestObject
            ->{OverviewKeyRequestInterface::KEY_TIME_SLOT_ID};

        if ($idMerchant === null || is_int($idMerchant) === false) {
            return;
        }

        if($version == 'v1' && ($idTimeSlot === null || is_int($idTimeSlot) === false))
        {
            return;
        }

        $concreteTimeSlotTransfer = $this
            ->getTimeSlotById($idTimeSlot);

        if ($concreteTimeSlotTransfer === null && $version === 'v1') {
            return;
        }

        $branchTransfer = $this
            ->getBranchTransferById($idMerchant);

        $items = $this
            ->transformCartItemList($requestObject);

        $this
            ->hydratePriceAndExpenses(
                $responseObject,
                $branchTransfer,
                $concreteTimeSlotTransfer,
                $items,
                $version,
                $requestObject
            );
    }

    /**
     * @param int $idTimeSlot
     * @return ConcreteTimeSlotTransfer|null
     */
    protected function getTimeSlotById(int $idTimeSlot): ?ConcreteTimeSlotTransfer
    {
        $concreteTimeSlotTransfers = $this
            ->client
            ->getTimeSlotsByIds(
                [
                    $idTimeSlot
                ]
            );

        if (count($concreteTimeSlotTransfers) < 1) {
            return null;
        }

        $concreteTimeSlotTransfer = reset(
            $concreteTimeSlotTransfers
        );

        return $concreteTimeSlotTransfer;
    }

    /**
     * @param int $idBranch
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
     * @param stdClass $requestObject
     * @return ArrayObject
     */
    protected function transformCartItemList(stdClass $requestObject): ArrayObject
    {
        $cartItems = $requestObject
            ->{OverviewKeyRequestInterface::KEY_CART};

        $itemTransfers = new ArrayObject();

        foreach ($cartItems as $cartItem) {
            $itemTransfers
                ->append(
                    $this->cartItemToItemTransfer($cartItem)
                );
        }

        return $itemTransfers;
    }

    /**
     * @param stdClass $cartItem
     * @return ItemTransfer
     */
    protected function cartItemToItemTransfer(stdClass $cartItem): ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku(
                $cartItem->{OverviewKeyRequestInterface::KEY_CART_SKU}
            )
            ->setQuantity(
                $cartItem->{OverviewKeyRequestInterface::KEY_CART_QUANTITY}
            );
    }

    /**
     * @param stdClass $responseObject
     * @param BranchTransfer $branchTransfer
     * @param ConcreteTimeSlotTransfer|null $concreteTimeSlotTransfer
     * @param ArrayObject $items
     * @param string $version
     * @param stdClass|null $requestObject
     */
    protected function hydratePriceAndExpenses(
        stdClass $responseObject,
        BranchTransfer $branchTransfer,
        ?ConcreteTimeSlotTransfer $concreteTimeSlotTransfer,
        ArrayObject $items,
        string $version,
        stdClass $requestObject = null
    ): void
    {
        if($concreteTimeSlotTransfer === null && $version === 'v2')
        {
            $cartChangeResponse = $this
                ->cartClient
                ->addItemsForBranchFlexTimeSlots(
                    $items->getArrayCopy(),
                    $branchTransfer,
                    $requestObject
                );
        }else{
            $cartChangeResponse = $this
                ->cartClient
                ->addItemsForBranchAndConcreteTimeSlot(
                    $items->getArrayCopy(),
                    $branchTransfer,
                    $concreteTimeSlotTransfer,
                    $requestObject
                );
        }

        $this
            ->hydrateTotals(
                $responseObject,
                $cartChangeResponse->getTotals()
            );

        $this
            ->hydrateExpenses(
                $responseObject,
                $cartChangeResponse->getExpenses()
            );

        $this
            ->hydrateItems(
                $responseObject,
                $cartChangeResponse->getItems()
            );
    }

    /**
     * @param stdClass $responseObject
     * @param TotalsTransfer $totalsTransfer
     * @return void
     */
    protected function hydrateTotals(
        stdClass $responseObject,
        TotalsTransfer $totalsTransfer
    ): void
    {
        $totals = new stdClass();

        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_SUBTOTAL} = $totalsTransfer->getSubtotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_EXPENSE} = $totalsTransfer->getExpenseTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_DISCOUNT} = $totalsTransfer->getDiscountTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_TAX} = $totalsTransfer->getTaxTotal()->getAmount();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_GRAND} = $totalsTransfer->getGrandTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_NET} = $totalsTransfer->getNetTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_DELIVERY_COST} = $totalsTransfer->getDeliveryCostTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_MISSING_MIN_AMOUNT} = $totalsTransfer->getMissingMinAmountTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_MISSING_MIN_UNITS} = $totalsTransfer->getMissingMinUnitsTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTAL_DEPOSIT} = $totalsTransfer->getDepositTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_WEIGHT} = $totalsTransfer->getWeightTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_DISPLAY} = $totalsTransfer->getDisplayTotal();
        $totals
            ->{OverviewKeyResponseInterface::KEY_TOTALS_GROSS_SUBTOTAL} = $totalsTransfer->getGrossSubtotal();

        $responseObject
            ->{OverviewKeyResponseInterface::KEY_TOTALS} = $totals;
    }

    /**
     * @param stdClass $responseObject
     * @param ArrayObject|ExpenseTransfer[] $expenses
     * @return void
     */
    protected function hydrateExpenses(
        stdClass $responseObject,
        ArrayObject $expenses
    ): void
    {
        $responseObject
            ->{OverviewKeyResponseInterface::KEY_EXPENSES} = [];

        foreach ($expenses as $expense) {
            $expenseObject = new stdClass();

            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_EXPENSE_TYPE} = $expense->getType();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_UNIT_GROSS_PRICE} = $expense->getUnitGrossPrice();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_SUM_GROSS_PRICE} = $expense->getSumGrossPrice();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_NAME} = $expense->getName();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_TAX_RATE} = $expense->getTaxRate();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_QUANTITY} = $expense->getQuantity();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_UNIT_PRICE} = $expense->getUnitPrice();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_SUM_PRICE} = $expense->getSumPrice();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_UNIT_PRICE_TO_PAY_AGGREGATION} = $expense->getUnitPriceToPayAggregation();
            $expenseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES_SUM_PRICE_TO_PAY_AGGREGATION} = $expense->getSumPriceToPayAggregation();

            $responseObject
                ->{OverviewKeyResponseInterface::KEY_EXPENSES}[] = $expenseObject;
        }
    }

    /**
     * @param stdClass $responseObject
     * @param ArrayObject|ItemTransfer[] $items
     * @return void
     */
    protected function hydrateItems(
        stdClass $responseObject,
        ArrayObject $items
    ): void
    {
        $responseObject
            ->{OverviewKeyResponseInterface::KEY_CART_ITEMS} = [];

        foreach ($items as $item) {
            $cartItem = new stdClass();

            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_NAME} = $this->getProductNameFromItemAttributes($item);
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_UNIT_NAME} = $this->getUnitNameFromItem($item);
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_UNIT_GROSS_PRICE} = $item->getUnitGrossPrice();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_QUANTITY} = $item->getQuantity();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_SUM_GROSS_PRICE} = $item->getSumGrossPrice();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_TAX_RATE} = $item->getTaxRate();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_SKU} = $item->getSku();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_UNIT_PRICE_TO_PAY_AGGREGATION} = $item->getUnitPriceToPayAggregation();
            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_SUM_PRICE_TO_PAY_AGGREGATION} = $item->getSumPriceToPayAggregation();

            $cartItem
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS} = [];

            foreach ($item->getCalculatedDiscounts() as $calculatedDiscount) {
                $discount = new stdClass();

                $discount
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS_UNIT_AMOUNT} = $calculatedDiscount->getUnitAmount();
                $discount
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS_SUM_AMOUNT} = $calculatedDiscount->getSumAmount();
                $discount
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS_DISPLAY_NAME} = $calculatedDiscount->getDisplayName();
                $discount
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS_QUANTITY} = $calculatedDiscount->getQuantity();
                $discount
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS_DISCOUNT_NAME} = $calculatedDiscount->getDiscountName();

                $cartItem
                    ->{OverviewKeyResponseInterface::KEY_CART_ITEMS_DISCOUNTS}[] = $discount;
            }

            $responseObject
                ->{OverviewKeyResponseInterface::KEY_CART_ITEMS}[] = $cartItem;
        }
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @return string
     */
    protected function getProductNameFromItemAttributes(ItemTransfer $itemTransfer) : string
    {
        if(array_key_exists(self::PRODUCT_NAME_ATTRIBUTE, $itemTransfer->getConcreteAttributes()) === true){
            return $itemTransfer->getConcreteAttributes()[self::PRODUCT_NAME_ATTRIBUTE];
        }

        return $itemTransfer->getName();
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @return string
     */
    protected function getUnitNameFromItem(ItemTransfer $itemTransfer) : string
    {
        return $itemTransfer->getDeposit()->getPresentationName();
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     * @param string $version
     * @return int
     */
    protected function getIdBranch(stdClass $requestObject, stdClass $responseObject, string $version) : int
    {
        if($version === 'v2'){
            return $requestObject->{OverviewKeyRequestInterface::KEY_BRANCH_ID};
        }

        return $responseObject
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT}
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_MERCHANT_ID};
    }
}
