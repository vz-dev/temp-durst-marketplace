<?php
/**
 * Durst - project - ItemReader.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.03.20
 * Time: 15:51
 */

namespace Pyz\Zed\Sales\Business\Model\Item;

use DateTime;
use DateTimeInterface;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemStateHistory;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class ItemReader implements ItemReaderInterface
{
    /**
     * @var \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * ItemReader constructor.
     * @param \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     */
    public function __construct(
        SalesQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer[]
     */
    public function getOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): array
    {
        $orderItems = $this
            ->queryContainer
            ->querySalesOrderItemsByBranchAndStateAndDateRange(
                $idBranch,
                $states,
                $start,
                $end
            )
            ->find();

        $items = [];

        foreach ($orderItems as $orderItem) {
            $items[] = $this
                ->entityToItemEntityTransfer($orderItem);
        }

        return $items;
    }

    /**
     * @param DateTimeInterface $startDate
     * @param array $processNames
     * @param array $stateNames
     * @return array|ItemTransfer[]
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function getOrderItemsByProcessesAndStates(
        DateTimeInterface $startDate,
        array $processNames = [],
        array $stateNames = []
    ): array {
        $subquery = $this
            ->queryContainer
            ->querySalesOrderItem()
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->filterByStartTime($startDate, Criteria::GREATER_THAN)
                ->endUse()
            ->endUse();

        if (count($processNames) > 0) {
            $subquery
                ->useProcessQuery()
                    ->filterByName_In($processNames)
                ->endUse();
        }

        if (count($stateNames) > 0) {
            $subquery
                ->useStateQuery()
                    ->filterByName_In($stateNames)
                ->endUse();
        }

        $subqueryOrderItems = $subquery
            ->find()
            ->getArrayCopy();

        $subqueryOrderItemIds = array_map(function(SpySalesOrderItem $orderItem) {
            return $orderItem->getIdSalesOrderItem();
        }, $subqueryOrderItems);

        $orderItems = $this
            ->queryContainer
            ->querySalesOrderItem()
            ->filterByIdSalesOrderItem_In($subqueryOrderItemIds)
            ->joinWithProcess()
            ->joinWithStateHistory()
            ->useStateHistoryQuery()
                ->joinWithState()
                ->orderByCreatedAt()
            ->endUse()
            ->find();

        $orderItemTransfers = [];

        foreach ($orderItems as $orderItem) {
            $orderItemTransfers[] = $this->entityToItemTransfer($orderItem);
        }

        return $orderItemTransfers;
    }

    /**
     * @param SpySalesOrderItem $orderItem
     * @return SpySalesOrderItemEntityTransfer
     */
    protected function entityToItemEntityTransfer(SpySalesOrderItem $orderItem): SpySalesOrderItemEntityTransfer
    {
        $transfer = new SpySalesOrderItemEntityTransfer();
        $transfer
            ->fromArray(
                $orderItem->toArray(),
                true
            );

        return $transfer;
    }

    /**
     * @param SpySalesOrderItem $orderItem
     * @return ItemTransfer
     * @throws PropelException
     */
    protected function entityToItemTransfer(SpySalesOrderItem $orderItem): ItemTransfer {
        $itemTransfer = (new ItemTransfer())
            ->fromArray(
                $orderItem->toArray(),
                true
            );

        $itemTransfer->setProcess($orderItem->getProcess()->getName());

        $stateHistories = $orderItem->getStateHistories();

        if ($stateHistories->isEmpty() !== true) {
            foreach ($stateHistories as $stateHistory) {
                $stateHistoryTransfer = $this->stateHistoryEntityToTransfer($stateHistory);

                $itemTransfer->addStateHistory($stateHistoryTransfer);
            }
        }

        return $itemTransfer;
    }

    /**
     * @throws PropelException
     */
    protected function stateHistoryEntityToTransfer(SpyOmsOrderItemStateHistory $stateHistory): ItemStateTransfer
    {
        return (new ItemStateTransfer())
            ->setIdSalesOrderItem($stateHistory->getFkSalesOrderItem())
            ->setName($stateHistory->getState()->getName())
            ->setCreatedAt($stateHistory->getCreatedAt()->format('Y-m-d H:i:s'));
    }
}
