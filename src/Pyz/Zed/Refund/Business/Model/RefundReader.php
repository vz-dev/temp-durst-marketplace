<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 13:00
 */

namespace Pyz\Zed\Refund\Business\Model;


use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Orm\Zed\Refund\Persistence\SpyRefund;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Refund\Persistence\RefundQueryContainerInterface;

class RefundReader implements RefundReaderInterface
{
    /**
     * @var RefundQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * RefundReader constructor.
     * @param RefundQueryContainerInterface $queryContainer
     */
    public function __construct(RefundQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateSalesOrderRefundInformation(OrderTransfer $orderTransfer): OrderTransfer
    {
        $refundTransfers = $this
            ->getRefundsBySalesOrderId(
                $orderTransfer->getIdSalesOrder()
            );

        foreach ($refundTransfers as $refundTransfer) {
            $orderTransfer->addRefund($refundTransfer);
        }

        return $orderTransfer;
    }

    /**
     * @param int[] $salesOrderIds
     * @param bool $excludeMissingOrderItems
     * @return RefundTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getRefundsBySalesOrderIds(array $salesOrderIds, bool $excludeMissingOrderItems = false): array
    {
        $refundTransfers = [];
        foreach ($salesOrderIds as $salesOrderId) {
            $refundTransfers = array_merge(
                $refundTransfers,
                $this->getRefundsBySalesOrderId($salesOrderId, $excludeMissingOrderItems)
            );
        }

        return $refundTransfers;
    }

    /**
     * @param SpyRefund $refund
     * @return RefundTransfer
     */
    protected function entityToTransfer(SpyRefund $refund): RefundTransfer
    {
        return (new RefundTransfer())
            ->fromArray($refund->toArray());
    }

    /**
     * @param int $salesOrderId
     * @param bool $excludeMissingOrderItems
     * @return RefundTransfer[]
     * @throws AmbiguousComparisonException
     */
    protected function getRefundsBySalesOrderId(int $salesOrderId, bool $excludeMissingOrderItems = false): array
    {
        $refundTransfers = [];

        $query = $this
            ->queryContainer
            ->queryRefunds()
            ->filterByFkSalesOrder($salesOrderId);

        if ($excludeMissingOrderItems === true) {
            $query
                ->useSpySalesOrderItemQuery()
                    ->filterByDeliveryStatus(
                        SalesConstants::ORDER_ITEM_DELIVERY_STATUS_MISSING,
                        Criteria::NOT_EQUAL
                    )
                    ->_or()
                    ->filterByDeliveryStatus(null, Criteria::ISNULL)
                ->endUse();
        }

        $refundEntities = $query->find();

        foreach ($refundEntities as $refundEntity) {
            $refundTransfers[] = $this
                ->entityToTransfer($refundEntity);
        }

        return $refundTransfers;
    }
}
