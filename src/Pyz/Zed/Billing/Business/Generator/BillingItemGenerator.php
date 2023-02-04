<?php
/**
 * Durst - project - BillingItemGenerator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-22
 * Time: 06:27
 */

namespace Pyz\Zed\Billing\Business\Generator;

use ArrayObject;
use Generated\Shared\Transfer\BillingItemTransfer;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Calculator\InvoicesCalculatorInterface;
use Pyz\Zed\Billing\Business\Model\BillingItemInterface;
use Pyz\Zed\Billing\Business\Model\BillingPeriodInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface;

class BillingItemGenerator implements BillingItemGeneratorInterface
{
    public const DEPOSIT_REFUND_TYPE_PREFIX = 'RETURNED_DEPOSIT_TYPE_';
    public const VOUCHER_DISCOUNT_EXPENSE_TYPE = 'VOUCHER_CODE_EXPENSE_TYPE';

    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface
     */
    protected $billingPeriod;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingItemInterface
     */
    protected $billingItem;

    /**
     * @var \Pyz\Zed\Billing\Business\Calculator\InvoicesCalculatorInterface
     */
    protected $invoicesCalculator;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * BillingItemGenerator constructor.
     *
     * @param \Pyz\Zed\Billing\BillingConfig $config
     * @param \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface $billingPeriod
     * @param \Pyz\Zed\Billing\Business\Model\BillingItemInterface $billingItem
     * @param \Pyz\Zed\Billing\Business\Calculator\InvoicesCalculatorInterface $invoicesCalculator
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface $salesFacade
     */
    public function __construct(
        BillingConfig $config,
        BillingPeriodInterface $billingPeriod,
        BillingItemInterface $billingItem,
        InvoicesCalculatorInterface $invoicesCalculator,
        BillingToSalesBridgeInterface $salesFacade
    ) {
        $this->config = $config;
        $this->billingPeriod = $billingPeriod;
        $this->billingItem = $billingItem;
        $this->invoicesCalculator = $invoicesCalculator;
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function createBillingItemsForEndedBillingPeriods(): void
    {
        $endedBillingPeriods = $this
            ->billingPeriod
            ->getBillingPeriodsByEndDate(date('Y-m-d', strtotime('yesterday')));

        foreach ($endedBillingPeriods as $endedBillingPeriod) {
            $this->createBillingItemsForBillingPeriod($endedBillingPeriod);
        }
    }

    /**
     * @param int $billingPeriodId
     *
     * @return void
     */
    public function createBillingItemsForBillingPeriodByBillingPeriodId(int $billingPeriodId) : void
    {
        $billingPeriod = $this
            ->billingPeriod
            ->getBillingPeriodById($billingPeriodId);

        $this->createBillingItemsForBillingPeriod($billingPeriod);
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    protected function createBillingItemsForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $orders = $this
            ->salesFacade
            ->getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId(
                $billingPeriodTransfer->getStartDate(),
                $billingPeriodTransfer->getEndDate(),
                $billingPeriodTransfer->getBranch()->getIdBranch()
            );

        foreach ($orders as $order) {
            $this->createBillingItemForOrderAndAddToPeriod($order, $billingPeriodTransfer);
        }

        $this
            ->invoicesCalculator
            ->calculateTotals($billingPeriodTransfer);

        $this
            ->billingPeriod
            ->updateBillingPeriod($billingPeriodTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    protected function createBillingItemForOrderAndAddToPeriod(OrderTransfer $orderTransfer, BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $voucherDiscountAmount = $this->getVoucherDiscountSumFromExpenses($orderTransfer->getExpenses());

        $billingItemTransfer = $this
            ->createBillingItemTransfer()
            ->setBillingPeriod($billingPeriodTransfer)
            ->setFkSalesOrder($orderTransfer->getIdSalesOrder())
            ->setAmount($orderTransfer->getTotals()->getGrandTotal())
            ->setDiscountAmount($orderTransfer->getTotals()->getDiscountTotal() - $voucherDiscountAmount)
            ->setVoucherDiscountAmount($voucherDiscountAmount)
            ->setReturnDepositAmount($this->getDepositRefundSumFromExpenses($orderTransfer->getExpenses()))
            ->setTaxAmount($orderTransfer->getTotals()->getTaxTotal()->getAmount())
            ->setTaxRateTotals($orderTransfer->getTotals()->getTaxRateTotals());

        $billingItemTransfer = $this
            ->billingItem
            ->createBillingItem($billingItemTransfer);

        $billingPeriodTransfer->addBillingItems($billingItemTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\BillingItemTransfer
     */
    protected function createBillingItemTransfer() : BillingItemTransfer
    {
        return new BillingItemTransfer();
    }

    /**
     * @param \ArrayObject $expenseTransfers
     *
     * @return int
     */
    protected function getDepositRefundSumFromExpenses(ArrayObject $expenseTransfers): int
    {
        $refundSum = 0;
        foreach ($expenseTransfers as $expenseTransfer) {
            if (strpos($expenseTransfer->getType(), static::DEPOSIT_REFUND_TYPE_PREFIX, 0) !== false) {
                $refundSum += $expenseTransfer->getSumPrice();
            }
        }

        return $refundSum;
    }

    /**
     * @param ArrayObject $expenseTransfers
     *
     * @return int
     */
    protected function getVoucherDiscountSumFromExpenses(ArrayObject $expenseTransfers): int
    {
        $voucherDiscountSum = 0;
        foreach ($expenseTransfers as $expenseTransfer) {
            if ($expenseTransfer->getType() === static::VOUCHER_DISCOUNT_EXPENSE_TYPE) {
                $voucherDiscountSum += ($expenseTransfer->getSumPriceToPayAggregation() * -1);
            }
        }

        return $voucherDiscountSum;
    }
}
