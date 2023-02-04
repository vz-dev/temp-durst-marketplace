<?php
/**
 * Durst - project - DepositOrderSaver.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.06.18
 * Time: 10:37
 */

namespace Pyz\Zed\Deposit\Business\Checkout;


use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Deposit\DepositConstants;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;

class DepositOrderSaver
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @var \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     */
    public function __construct(SalesQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     * @throws \Throwable
     */
    public function saveOrderDeposit(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this->handleDatabaseTransaction(function () use ($quoteTransfer, $saveOrderTransfer) {
            $this->saveOrderDepositTransaction($quoteTransfer, $saveOrderTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function saveOrderDepositTransaction(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $salesOrderEntity = $this->getSalesOrderByIdSalesOrder($saveOrderTransfer->getIdSalesOrder());

        $this->addExpensesToOrder($quoteTransfer, $salesOrderEntity, $saveOrderTransfer);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesExpense $salesOrderExpenseEntity
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return void
     */
    protected function hydrateOrderExpenseEntity(
        SpySalesExpense $salesOrderExpenseEntity,
        ExpenseTransfer $expenseTransfer
    ) {
        $salesOrderExpenseEntity->fromArray($expenseTransfer->toArray());
        $salesOrderExpenseEntity->setGrossPrice($expenseTransfer->getUnitGrossPrice());
        $salesOrderExpenseEntity->setNetPrice($expenseTransfer->getUnitGrossPrice() - $expenseTransfer->getUnitTaxAmount());
        $salesOrderExpenseEntity->setPrice($expenseTransfer->getUnitPrice());
        $salesOrderExpenseEntity->setQuantity($expenseTransfer->getQuantity());
        $salesOrderExpenseEntity->setTaxAmount($expenseTransfer->getUnitTaxAmount());
        $salesOrderExpenseEntity->setDiscountAmountAggregation($expenseTransfer->getUnitDiscountAmountAggregation());
        $salesOrderExpenseEntity->setPriceToPayAggregation($expenseTransfer->getUnitPriceToPayAggregation());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function addExpensesToOrder(
        QuoteTransfer $quoteTransfer,
        SpySalesOrder $salesOrderEntity,
        SaveOrderTransfer $saveOrderTransfer
    ) {
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if($this->isDepositExpense($expenseTransfer) || $this->isDepositReturnExpense($expenseTransfer)){
                $salesOrderExpenseEntity = new SpySalesExpense();
                $this->hydrateOrderExpenseEntity($salesOrderExpenseEntity, $expenseTransfer);
                $salesOrderExpenseEntity->setFkSalesOrder($salesOrderEntity->getIdSalesOrder());
                $salesOrderExpenseEntity->save();

                $this->setCheckoutResponseExpenses($saveOrderTransfer, $expenseTransfer, $salesOrderExpenseEntity);

                $salesOrderEntity->addExpense($salesOrderExpenseEntity);
            }
        }
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     * @return bool
     */
    protected function isDepositExpense(ExpenseTransfer $expenseTransfer) : bool
    {
        return (
            substr(
                $expenseTransfer->getType(),
                0,
                strlen(DepositConstants::DEPOSIT_EXPENSE_TYPE)
            ) === DepositConstants::DEPOSIT_EXPENSE_TYPE
        );
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     * @return bool
     */
    protected function isDepositReturnExpense(ExpenseTransfer $expenseTransfer) : bool
    {
        return (
            $expenseTransfer->getType() === DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE
        );
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function getSalesOrderByIdSalesOrder($idSalesOrder)
    {
        return $this
            ->queryContainer
            ->querySalesOrderById($idSalesOrder)
            ->findOne();
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesExpense $salesOrderExpenseEntity
     *
     * @return void
     */
    protected function setCheckoutResponseExpenses(
        SaveOrderTransfer $saveOrderTransfer,
        ExpenseTransfer $expenseTransfer,
        SpySalesExpense $salesOrderExpenseEntity
    ) {
        $orderExpense = clone $expenseTransfer;
        $orderExpense->setIdSalesExpense($salesOrderExpenseEntity->getIdSalesExpense());
        $saveOrderTransfer->addOrderExpense($orderExpense);
    }
}
