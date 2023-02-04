<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-06
 * Time: 09:47
 */

namespace Pyz\Zed\Oms\Persistence;

use DateTime;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Oms\Persistence\SpyOmsTransitionLogQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\Persistence\OmsQueryContainer as SprykerOmsQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class OmsQueryContainer extends SprykerOmsQueryContainer implements OmsQueryContainerInterface
{

    /**
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrdersForConcreteTourInState(array $processIds, array $stateIds, int $idConcreteTour): SpySalesOrderItemQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->filterByFkConcreteTour($idConcreteTour)
                ->endUse()
            ->endUse()
            ->filterByFkOmsOrderProcess($processIds, Criteria::IN)
            ->filterByFkOmsOrderItemState($stateIds, Criteria::IN)
            ->orderByMerchantSku();
    }

    /**
     * @param array $processIds
     * @param array $stateIds
     * @param int $idGraphmastersTour
     * @return SpySalesOrderItemQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrdersForGraphmastersTourInState(
        array $processIds,
        array $stateIds,
        int $idGraphmastersTour
    ): SpySalesOrderItemQuery {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useDstGraphmastersOrderQuery()
                    ->filterByFkGraphmastersTour($idGraphmastersTour)
                ->endUse()
            ->endUse()
            ->filterByFkOmsOrderProcess($processIds, Criteria::IN)
            ->filterByFkOmsOrderItemState($stateIds, Criteria::IN)
            ->orderByMerchantSku();
    }

    /**
     * {@inheritDoc}
     *
     * @param array $processIds
     * @param array $stateIds
     * @param DriverTransfer $driverTransfer
     * @param DateTime $dateTime
     * @return SpySalesOrderItemQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrdersForDriverInState(
        array $processIds,
        array $stateIds,
        DriverTransfer $driverTransfer,
        DateTime $dateTime
    ): SpySalesOrderItemQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->useDstConcreteTourQuery()
                        ->filterByDate($dateTime, Criteria::LESS_THAN)
                        ->filterByFkBranch($driverTransfer->getFkBranch())
                        ->filterByFkDriver($driverTransfer->getIdDriver())
                        ->addOr(DstConcreteTourTableMap::COL_FK_DRIVER, null, Criteria::ISNULL)
                    ->endUse()
                ->endUse()
                ->orderByDeliveryOrder()
            ->endUse()
            ->filterByFkOmsOrderProcess($processIds, Criteria::IN)
            ->filterByFkOmsOrderItemState($stateIds, Criteria::IN)
            ->orderByMerchantSku();
    }

    /**
     * {@inheritDoc}
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @return SpySalesOrderQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrdersInStateForConcreteTour(
        array $processIds,
        array $stateIds,
        int $idConcreteTour
    ): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->useItemQuery()
                ->filterByFkOmsOrderProcess($processIds, Criteria::IN)
                ->filterByFkOmsOrderItemState($stateIds, Criteria::IN)
            ->endUse()
            ->useSpyConcreteTimeSlotQuery()
                ->filterByFkConcreteTour($idConcreteTour)
            ->endUse()
            ->distinct();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string $sourceState
     * @return SpyOmsTransitionLogQuery
     * @throws AmbiguousComparisonException
     */
    public function queryTransitionLogByIdSalesOrderAndSourceState(
        int $idSalesOrder,
        string $sourceState
    ): SpyOmsTransitionLogQuery
    {
        return $this
            ->getFactory()
            ->createOmsTransitionLogQuery()
            ->filterBySourceState(
                $sourceState
            )
            ->orderByCreatedAt()
            ->useOrderQuery()
                ->filterByIdSalesOrder(
                    $idSalesOrder
                )
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return SpySalesOrderItemQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrderItemsForConcreteTourWithDeliveryStatus(int $idConcreteTour): SpySalesOrderItemQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->filterByFkConcreteTour($idConcreteTour)
                ->endUse()
            ->endUse()
            ->filterByDeliveryStatus(null, Criteria::ISNOTNULL)
            ->orderByMerchantSku();
    }

    /**
     * {@inheritDoc}
     *
     * @param array $processIds
     * @param array $stateIds
     * @param int $idConcreteTour
     * @throws AmbiguousComparisonException
     */
    public function queryOrderItemsForConcreteTourInStateOrWithDeliveryStatus(
        array $processIds,
        array $stateIds,
        int $idConcreteTour
    ): SpySalesOrderItemQuery {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->filterByFkConcreteTour($idConcreteTour)
                ->endUse()
            ->endUse()
            ->filterByFkOmsOrderProcess($processIds, Criteria::IN)
            ->filterByFkOmsOrderItemState($stateIds, Criteria::IN)
            ->_or()
            ->filterByDeliveryStatus([
                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DELIVERED,
                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DECLINED,
                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DAMAGED,
                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_MISSING
            ], Criteria::IN)
            ->orderByMerchantSku();
    }
}
