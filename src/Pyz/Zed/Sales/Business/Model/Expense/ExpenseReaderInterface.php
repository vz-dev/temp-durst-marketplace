<?php
/**
 * Durst - project - ExpenseReaderInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-03
 * Time: 22:28
 */

namespace Pyz\Zed\Sales\Business\Model\Expense;


use Generated\Shared\Transfer\ExpenseTransfer;
use Pyz\Zed\Sales\Business\Exceptions\ExpenseWithIdNotFoundException;

interface ExpenseReaderInterface
{
    /**
     * @param int $idExpense
     * @return ExpenseTransfer
     * @throws ExpenseWithIdNotFoundException
     */
    public function getExpenseById(int $idExpense) : ExpenseTransfer;
}