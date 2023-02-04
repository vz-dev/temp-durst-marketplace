<?php
/**
 * Durst - project - SalesExpenseDepositDeflator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-04
 * Time: 16:09
 */

namespace Pyz\Zed\Deposit\Business\Sales;

use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Deposit\Business\Model\DepositManager;

class SalesExpenseDepositDeflator implements SalesExpenseDepositDeflatorInterface
{
    /**
     * @var ExpenseTransfer[]
     */
    protected $newExpenses = [];

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function deflateSalesExpenses(OrderTransfer $orderTransfer) : OrderTransfer
    {
        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($expense->getName() === DepositManager::DEPOSIT_EXPENSE_NAME && $expense->getQuantity() == 1) {
                $depositExpense = new ExpenseTransfer();

                $depositSku = $this->getDepositSkuFromDepositTypeString($expense->getType());
                if($depositSku !== ""){
                    $depositExpense->fromArray($expense->toArray());
                    $depositExpense->setQuantity($this->getCurrentQuantity($depositSku) + 1);
                    $depositExpense->setType(sprintf('deposit-%s', $depositSku));

                    $this->newExpenses[$depositSku] = $depositExpense;
                }

                continue;
            }

            $this->newExpenses[$expense->getIdSalesExpense()] = $expense;
        }

        $this->addNewExpensesToOrderTransfer($orderTransfer);

        return $orderTransfer;
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    protected function getCurrentQuantity(string $sku): int
    {
        if (array_key_exists($sku, $this->newExpenses) === true) {
            return $this->newExpenses[$sku]->getQuantity();
        }
        return 0;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function addNewExpensesToOrderTransfer(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer->setExpenses(new ArrayObject());
        $orderTransfer->setExpenses($this->newExpenses);

        return $orderTransfer;
    }

    /**
     * @param string $depositType
     * @return string
     */
    protected function getDepositSkuFromDepositTypeString(string $depositType) : string
    {
        $matches = [];
        preg_match('/deposit-([0-9]+)-([0-9])/', $depositType, $matches);

        if (!empty($matches && $matches[1]) === true) {
            return $matches[1];
        }

        return "";
    }
}
