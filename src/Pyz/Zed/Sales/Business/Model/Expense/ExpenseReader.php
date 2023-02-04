<?php
/**
 * Durst - merchant_center - ExpenseReader.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-25
 * Time: 13:10
 */

namespace Pyz\Zed\Sales\Business\Model\Expense;


use Generated\Shared\Transfer\ExpenseTransfer;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Pyz\Zed\Sales\Business\Exceptions\ExpenseWithIdNotFoundException;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;

class ExpenseReader implements ExpenseReaderInterface
{
    /**
     * @var SalesQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * ExpenseReader constructor.
     * @param SalesQueryContainerInterface $queryContainer
     */
    public function __construct(SalesQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idExpense
     * @return ExpenseTransfer
     * @throws ExpenseWithIdNotFoundException
     */
    public function getExpenseById(int $idExpense) : ExpenseTransfer
    {
        $expense = $this
            ->queryContainer
            ->querySalesExpense()
            ->findOneByIdSalesExpense($idExpense);

        if($expense === null){
            throw new ExpenseWithIdNotFoundException(
                sprintf(ExpenseWithIdNotFoundException::MESSAGE, $idExpense)
            );
        }

        return $this->entityToTransfer($expense);
    }

    /**
     * @param SpySalesExpense $expense
     * @return ExpenseTransfer
     */
    protected function entityToTransfer(SpySalesExpense $expense): ExpenseTransfer
    {
        $expenseTransfer = new ExpenseTransfer();
        $expenseTransfer->fromArray($expense->toArray(), true);

        return $expenseTransfer;
    }
}