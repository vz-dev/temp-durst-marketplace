<?php
/**
 * Durst - project - SalesQueryContainer.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 11:44
 */

namespace Pyz\Zed\Sales\Persistence;

use DateTime;
use Orm\Zed\Sales\Persistence\SpySalesExpenseQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderCommentQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Deposit\DepositConstants;
use Pyz\Shared\Sales\SalesConstants;
use \Spryker\Zed\Sales\Persistence\SalesQueryContainer as SprykerSalesQueryContainer;

class SalesQueryContainer extends SprykerSalesQueryContainer implements SalesQueryContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return $this|\Orm\Zed\Sales\Persistence\SpySalesOrderCommentQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryCustomerCommentsByIdSalesOrder(int $idSalesOrder) : SpySalesOrderCommentQuery
    {
        return $this->getFactory()
                    ->createSalesOrderCommentQuery()
                    ->filterByFkSalesOrder($idSalesOrder)
                    ->filterByType(SalesConstants::COMMENT_TYPE_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return $this|\Orm\Zed\Sales\Persistence\SpySalesOrderCommentQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantCommentsByIdSalesOrder(int $idSalesOrder) : SpySalesOrderCommentQuery
    {
        return $this->getFactory()
            ->createSalesOrderCommentQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->filterByType(SalesConstants::COMMENT_TYPE_MERCHANT);
    }

    /**
     * @param array $processIds
     * @param array $stateBlacklist
     * @param int $idBranch
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryOrderItemsForConcreteTourInState(array $processIds, array $stateBlacklist, int $idConcreteTour) : SpySalesOrderItemQuery
    {
        $query = $this->getFactory()
            ->createSalesOrderItemQuery()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                ->filterByFkConcreteTour($idConcreteTour)
                ->endUse()
            ->endUse()
            ->filterByFkOmsOrderProcess($processIds, Criteria::IN);

        if ($stateBlacklist) {
            $query->filterByFkOmsOrderItemState($stateBlacklist, Criteria::NOT_IN);
        }

        return $query;

    }

    /**
     * {@inheritdoc}
     *
     * @param array $idOrders
     * @return SpySalesExpenseQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryDepositReturnSalesExpensesByOrderIds(array $idOrders): SpySalesExpenseQuery
    {
        return $this
            ->getFactory()
            ->createSalesExpenseQuery()
            ->filterByType(sprintf(
                '%s%%',
                DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE
            ), Criteria::LIKE)
            ->filterByFkSalesOrder_In($idOrders);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return int
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function querySalesOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): SpySalesOrderItemQuery
    {
        return $this
            ->getFactory()
            ->createSalesOrderItemQuery()
            ->useOrderQuery()
                ->filterByFkBranch($idBranch)
                ->filterByInvoiceCreatedAt($start, Criteria::GREATER_EQUAL)
                ->filterByInvoiceCreatedAt($end, Criteria::LESS_EQUAL)
            ->endUse()
            ->filterByFkOmsOrderItemState_In($states);
    }
}
