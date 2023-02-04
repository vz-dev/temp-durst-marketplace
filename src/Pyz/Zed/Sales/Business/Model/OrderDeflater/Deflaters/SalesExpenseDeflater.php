<?php
/**
 * Durst - project - SalesExpenseDeflater.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-01
 * Time: 11:14
 */

namespace Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters;


use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class SalesExpenseDeflater implements SalesExpenseDeflaterInterface
{
    public const DEPOSIT_EXPENSE_TYPE_PREFIX = 'deposit';
    /**
     * @var array
     */
    protected $newSalesExpenses = [];

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesExpenses(OrderTransfer $orderTransfer) : OrderTransfer
    {
        foreach ($orderTransfer->getExpenses() as $expense) {
            $type = $expense->getType();
            $expenseTransfer = new ExpenseTransfer();
            $expenseTransfer->fromArray($expense->toArray());

            if($this->typeStartsWithDepositTypePrefix($expense->getType())){
                $type = $this->getSkuFromExpenseTypeString($expense->getType());
                $expenseTransfer->setQuantity($this->getCurrentQuantity($type) + $expense->getQuantity());
                $expenseTransfer->setSumPrice($this->getCurrentSumPrice($type) + $expense->getSumPrice());
            }

            $this->newSalesExpenses[$type] = $expenseTransfer;
        }

        $this->addNewSalesExpenseToOrderTransfer($orderTransfer);

        return $orderTransfer;
    }

    /**
     * @param string $type
     *
     * @return int
     */
    protected function getCurrentQuantity(string $type): int
    {
        if (array_key_exists($type, $this->newSalesExpenses) === true) {
            return $this->newSalesExpenses[$type]->getQuantity();
        }
        return 0;
    }

    /**
     * @param string $type
     *
     * @return int
     */
    protected function getCurrentSumPrice(string $type): int
    {
        if (array_key_exists($type, $this->newSalesExpenses) === true) {
            return $this->newSalesExpenses[$type]->getSumPrice();
        }
        return 0;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function addNewSalesExpenseToOrderTransfer(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer->setExpenses(new ArrayObject($this->newSalesExpenses));

        return $orderTransfer;
    }

    /**
     * @param string $expenseType
     * @return string
     */
    protected function getSkuFromExpenseTypeString(string $expenseType) : string
    {
        return substr($expenseType, 0, strrpos( $expenseType, '-'));
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function typeStartsWithDepositTypePrefix(string $type) : bool
    {
        return strpos($type, static::DEPOSIT_EXPENSE_TYPE_PREFIX) === 0;
    }
}
