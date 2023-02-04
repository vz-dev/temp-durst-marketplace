<?php
/**
 * Durst - project - Checker.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.03.19
 * Time: 11:39
 */

namespace Pyz\Zed\Deposit\Business\Order;

use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;

class Checker implements CheckerInterface
{
    /**
     * @var \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * Checker constructor.
     *
     * @param \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(SalesQueryContainerInterface $salesQueryContainer)
    {
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * @param int $idSalesOrder
     * @return bool
     */
    public function hasOrderDepositReturns(int $idSalesOrder): bool
    {
        return ($this
            ->salesQueryContainer
            ->queryDepositReturnSalesExpensesByOrderIds([
                $idSalesOrder,
            ])
            ->count() > 0);
    }
}
