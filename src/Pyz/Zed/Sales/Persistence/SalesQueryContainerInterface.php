<?php
/**
 * Durst - project - SalesQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 12:35
 */

namespace Pyz\Zed\Sales\Persistence;

use DateTime;
use Orm\Zed\Sales\Persistence\SpySalesExpenseQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface as SprykerSalesQueryContainerInterface;

interface SalesQueryContainerInterface extends SprykerSalesQueryContainerInterface
{
    /**
     * Query Comments based on sales order id and filter based on type 'customer'
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderCommentQuery
     */
    public function queryCustomerCommentsByIdSalesOrder(int $idSalesOrder);

    /**
     * Query Comments based on sales order id and filter based on type 'merchant'
     *
     * @param int $idSalesOrder
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderCommentQuery
     */
    public function queryMerchantCommentsByIdSalesOrder(int $idSalesOrder);

    /**
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     */
    public function queryOrderItemsForConcreteTourInState(array $processIds, array $stateIds, int $idConcreteTour) : SpySalesOrderItemQuery;

    /**
     * Query sales expenses on an array of sales order ids
     *
     * @param array $idOrders
     * @return SpySalesExpenseQuery
     */
    public function queryDepositReturnSalesExpensesByOrderIds(array $idOrders): SpySalesExpenseQuery;

    /**
     * Query sales items and return the items filtered by:
     * - branch
     * - state
     * - date start
     * - date end
     *
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery
     */
    public function querySalesOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): SpySalesOrderItemQuery;

}
