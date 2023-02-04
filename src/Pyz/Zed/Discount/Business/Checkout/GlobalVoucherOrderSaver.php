<?php
/**
 * Durst - project - GlobalVoucherOrderSaver.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.02.21
 * Time: 14:55
 */

namespace Pyz\Zed\Discount\Business\Checkout;


use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Discount\DiscountConstants;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

class GlobalVoucherOrderSaver
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @var \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * GlobalVoucherOrderSaver constructor.
     * @param \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     */
    public function __construct(
        SalesQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws \Throwable
     */
    public function saveOrderGlobalVoucher(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        $this->handleDatabaseTransaction(
            function () use ($quoteTransfer, $saveOrderTransfer) {
                $this
                    ->saveOrderGlobalVoucherTransaction(
                        $quoteTransfer,
                        $saveOrderTransfer
                    );
            }
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function saveOrderGlobalVoucherTransaction(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        $salesOrderEntity = $this
            ->getSalesOrderByIdSalesOrder(
                $saveOrderTransfer
                    ->getIdSalesOrder()
            );

        $this
            ->addExpensesToOrder(
                $quoteTransfer,
                $salesOrderEntity,
                $saveOrderTransfer
            );
    }

    /**
     * @param int $idSalesOrder
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function getSalesOrderByIdSalesOrder(int $idSalesOrder): SpySalesOrder
    {
        return $this
            ->queryContainer
            ->querySalesOrderById($idSalesOrder)
            ->findOne();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $spySalesOrder
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function addExpensesToOrder(
        QuoteTransfer $quoteTransfer,
        SpySalesOrder $spySalesOrder,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        foreach ($quoteTransfer->getExpenses() as $expense) {
            if ($expense->getType() !== DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                continue;
            }

            $salesOrderExpenseEntity = new SpySalesExpense();

            $this
                ->hydrateOrderExpenseEntity(
                    $salesOrderExpenseEntity,
                    $expense
                );

            $salesOrderExpenseEntity
                ->setFkSalesOrder(
                    $spySalesOrder
                        ->getIdSalesOrder()
                );

            $salesOrderExpenseEntity
                ->save();

            $this
                ->setCheckoutResponseExpenses(
                    $saveOrderTransfer,
                    $expense,
                    $salesOrderExpenseEntity
                );

            $spySalesOrder
                ->addExpense(
                    $salesOrderExpenseEntity
                );
        }
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesExpense $salesExpense
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @return void
     */
    protected function hydrateOrderExpenseEntity(
        SpySalesExpense $salesOrderExpenseEntity,
        ExpenseTransfer $expenseTransfer
    ): void
    {
        $salesOrderExpenseEntity
            ->fromArray(
                $expenseTransfer
                    ->toArray()
            );

        $salesOrderExpenseEntity
            ->setGrossPrice($expenseTransfer->getUnitGrossPrice());
        $salesOrderExpenseEntity
            ->setNetPrice($expenseTransfer->getUnitGrossPrice() - $expenseTransfer->getUnitTaxAmount());
        $salesOrderExpenseEntity
            ->setPrice($expenseTransfer->getUnitPrice());
        $salesOrderExpenseEntity
            ->setTaxAmount($expenseTransfer->getUnitTaxAmount());
        $salesOrderExpenseEntity
            ->setDiscountAmountAggregation($expenseTransfer->getUnitDiscountAmountAggregation());
        $salesOrderExpenseEntity
            ->setPriceToPayAggregation($expenseTransfer->getUnitPriceToPayAggregation());
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesExpense $salesOrderExpenseEntity
     * @return void
     */
    protected function setCheckoutResponseExpenses(
        SaveOrderTransfer $saveOrderTransfer,
        ExpenseTransfer $expenseTransfer,
        SpySalesExpense $salesOrderExpenseEntity
    ): void
    {
        $orderExpense = clone $expenseTransfer;
        $orderExpense->setIdSalesExpense($salesOrderExpenseEntity->getIdSalesExpense());
        $saveOrderTransfer->addOrderExpense($orderExpense);
    }
}
