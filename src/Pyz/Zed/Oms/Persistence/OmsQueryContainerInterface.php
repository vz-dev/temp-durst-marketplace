<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-06
 * Time: 09:46
 */

namespace Pyz\Zed\Oms\Persistence;

use DateTime;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Oms\Persistence\SpyOmsTransitionLogQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Oms\Persistence\OmsQueryContainerInterface as SprykerOmsQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface OmsQueryContainerInterface extends SprykerOmsQueryContainerInterface
{

    /**
     * Receive a query with all orders in the given state related to the given process that
     * should be delivered in the given concrete tour
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     */
    public function queryOrdersForConcreteTourInState(array $processIds, array $stateIds, int $idConcreteTour): SpySalesOrderItemQuery;

    /**
     * Receive a query with all orders in the given state related to the given process that
     * should be delivered in the given Graphmasters tour
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idGraphmastersTour
     * @return SpySalesOrderItemQuery
     */
    public function queryOrdersForGraphmastersTourInState(
        array $processIds,
        array $stateIds,
        int $idGraphmastersTour
    ): SpySalesOrderItemQuery;

    /**
     * Create a query with all orders in the given state related to the given process
     * that should be delivered by the given driver
     *
     * @param array $processIds
     * @param array $stateIds
     * @param DriverTransfer $driverTransfer
     * @param DateTime $dateTime
     * @return SpySalesOrderItemQuery
     */
    public function queryOrdersForDriverInState(
        array $processIds,
        array $stateIds,
        DriverTransfer $driverTransfer,
        DateTime $dateTime
    ): SpySalesOrderItemQuery;

    /**
     * Create a query with all orders in the given state related to the given process that
     * should be delivered in the given concrete tour
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderQuery
     */
    public function queryOrdersInStateForConcreteTour(
        array $processIds,
        array $stateIds,
        int $idConcreteTour
    ): SpySalesOrderQuery;

    /**
     * Get the transition logs for an order identified by its id
     * where the source state in the transition log has the given state
     *
     * @param int $idSalesOrder
     * @param string $sourceState
     * @return SpyOmsTransitionLogQuery
     */
    public function queryTransitionLogByIdSalesOrderAndSourceState(
        int $idSalesOrder,
        string $sourceState
    ): SpyOmsTransitionLogQuery;

    /**
     * Returns a query with all order items that have a delivery status for
     * the tour with the specified ID
     *
     *
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrderItemsForConcreteTourWithDeliveryStatus(int $idConcreteTour): SpySalesOrderItemQuery;

    /**
     * Returns a query with all order items that have one of the specified states
     * or have a delivery status (except "cancelled") for the tour with the
     * specified ID
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     */
    public function queryOrderItemsForConcreteTourInStateOrWithDeliveryStatus(
        array $processIds,
        array $stateIds,
        int $idConcreteTour
    ): SpySalesOrderItemQuery;
}
