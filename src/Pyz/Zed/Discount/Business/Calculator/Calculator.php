<?php
/**
 * Durst - project - Calculator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.02.21
 * Time: 16:02
 */

namespace Pyz\Zed\Discount\Business\Calculator;

use Generated\Shared\Transfer\CalculatedDiscountTransfer;
use Generated\Shared\Transfer\CollectedDiscountTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Shared\Discount\DiscountConstants;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface;
use Spryker\Zed\Discount\Business\Calculator\Calculator as SprykerCalculator;
use Spryker\Zed\Discount\Business\Distributor\DistributorInterface;
use Spryker\Zed\Discount\Business\QueryString\SpecificationBuilderInterface;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToMessengerInterface;

class Calculator extends SprykerCalculator implements CalculatorInterface
{
    /**
     * @var array|CollectedDiscountTransfer[]
     */
    protected $globalVoucherDiscounts = [];

    /**
     * @var \Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface
     */
    protected $taxFacade;

    /**
     * Calculator constructor.
     * @param \Spryker\Zed\Discount\Business\QueryString\SpecificationBuilderInterface $collectorBuilder
     * @param \Spryker\Zed\Discount\Dependency\Facade\DiscountToMessengerInterface $messengerFacade
     * @param \Spryker\Zed\Discount\Business\Distributor\DistributorInterface $distributor
     * @param \Spryker\Zed\Discount\Dependency\Plugin\DiscountCalculatorPluginInterface[] $calculatorPlugins
     * @param DiscountToTaxBridgeInterface $taxFacade
     */
    public function __construct(
        SpecificationBuilderInterface $collectorBuilder,
        DiscountToMessengerInterface $messengerFacade,
        DistributorInterface $distributor,
        array $calculatorPlugins,
        DiscountToTaxBridgeInterface $taxFacade
    )
    {
        parent::__construct(
            $collectorBuilder,
            $messengerFacade,
            $distributor,
            $calculatorPlugins
        );

        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountTransfer[] $discounts
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @return CollectedDiscountTransfer[]
     */
    public function calculate(array $discounts, QuoteTransfer $quoteTransfer): array
    {
        $collectedDiscounts = $this->calculateDiscountAmount($discounts, $quoteTransfer);
        $collectedDiscounts = $this->filterExclusiveDiscounts($collectedDiscounts);
        $this->distributeDiscountAmount($collectedDiscounts);

        $this
            ->addGlobalVoucherExpense(
                $quoteTransfer
            );

        return $collectedDiscounts;
    }

    /**
     * @param \Generated\Shared\Transfer\CollectedDiscountTransfer[] $collectedDiscountsTransfer
     * @return void
     */
    protected function distributeDiscountAmount(array $collectedDiscountsTransfer)
    {
        foreach ($collectedDiscountsTransfer as $collectedDiscountTransfer) {
            if ($this->isGlobalVoucherDiscount($collectedDiscountTransfer)) {
                $this->globalVoucherDiscounts[] = $collectedDiscountTransfer;
                continue;
            }

            $this->distributor->distributeDiscountAmountToDiscountableItems($collectedDiscountTransfer);
            $this->setSuccessfulDiscountAddMessage($collectedDiscountTransfer->getDiscount());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CollectedDiscountTransfer $collectedDiscountTransfer
     * @return bool
     */
    protected function isGlobalVoucherDiscount(CollectedDiscountTransfer $collectedDiscountTransfer): bool
    {
        return ($collectedDiscountTransfer->getDiscount()->getDiscountType() === DiscountConstants::TYPE_VOUCHER);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @return void
     */
    protected function addGlobalVoucherExpense(
        QuoteTransfer $quoteTransfer
    ): void
    {
        foreach ($this->globalVoucherDiscounts as $globalVoucherDiscount) {
            $voucherExpense = $this
                ->createGlobalVoucherExpense(
                    $globalVoucherDiscount
                );

            $this
                ->addExpenseToQuoteAndConcreteTimeslots(
                    $quoteTransfer,
                    $voucherExpense
                );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CollectedDiscountTransfer $collectedDiscountTransfer
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createGlobalVoucherExpense(CollectedDiscountTransfer $collectedDiscountTransfer): ExpenseTransfer
    {
        $expenseAmount = 0;

        return (new ExpenseTransfer())
            ->setType(DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE)
            ->setName($collectedDiscountTransfer->getDiscount()->getDiscountName())
            ->setTaxRate($this->taxFacade->getDefaultTaxRateForDate(new \DateTime('now')))
            ->setQuantity(1)
            ->setSumPrice($expenseAmount)
            ->setUnitGrossPrice($expenseAmount)
            ->setSumGrossPrice($expenseAmount)
            ->setUnitPrice($expenseAmount)
            ->addCalculatedDiscount($this->convertDiscountToCalculatedDiscount($collectedDiscountTransfer->getDiscount()))
            ->setIdentifier($collectedDiscountTransfer->getDiscount()->getVoucherCode());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @return void
     */
    protected function addExpenseToQuoteAndConcreteTimeslots(
        QuoteTransfer $quoteTransfer,
        ExpenseTransfer $expenseTransfer
    ): void
    {
        foreach ($quoteTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
            $concreteTimeSlot
                ->addExpenses(
                    $expenseTransfer
                );
        }

        $quoteTransfer
            ->addExpense(
                $expenseTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountTransfer $discountTransfer
     * @return \Generated\Shared\Transfer\CalculatedDiscountTransfer
     */
    protected function convertDiscountToCalculatedDiscount(DiscountTransfer $discountTransfer): CalculatedDiscountTransfer
    {
        $transfer = (new CalculatedDiscountTransfer())
            ->fromArray(
                $discountTransfer
                    ->toArray(),
                true
            );

        $amount = $discountTransfer
            ->getAmount();

        $transfer
            ->setQuantity(1)
            ->setSumAmount($amount)
            ->setUnitAmount($amount)
            ->setSumGrossAmount($amount)
            ->setUnitGrossAmount($amount);

        return $transfer;
    }
}
