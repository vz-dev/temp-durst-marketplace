<?php
/**
 * Durst - project - ExpensesHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 15:02
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;

use Generated\Shared\Transfer\ExpenseTransfer;

interface ExpensesHydratorInterface
{
    /**
     * @param int $idBranch
     * @param array $itemData
     * @param string $sku
     * @param int $count
     *
     * @return ExpenseTransfer
     */
    public function createExpense(
        int $idBranch,
        array $itemData,
        string $sku,
        int $count
    ): ExpenseTransfer;
}
