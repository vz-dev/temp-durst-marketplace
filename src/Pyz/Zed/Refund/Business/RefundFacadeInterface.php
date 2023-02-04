<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 12:46
 */

namespace Pyz\Zed\Refund\Business;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Refund\Business\RefundFacadeInterface as SprykerRefundFacadeInterface;

interface RefundFacadeInterface extends SprykerRefundFacadeInterface
{
    /**
     * Add all refund information to an order transfer object
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderRefundInformation(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Set hasReturnItem flag on OrderTransfer if the given order has at least one refund entry
     * identified by sales order id
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Returns RefundTransfer[] for all refunds that belong to the sales orders
     * matching the provided salesOrderIds array, optionally excluding those for
     * order items that have been marked missing
     *
     * @param int[] $salesOrderIds
     * @param bool $excludeMissingOrderItems
     * @return RefundTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getSalesOrderRefundsBySalesOrderIds(
        array $salesOrderIds,
        bool $excludeMissingOrderItems = false
    ): array;

    /**
     * Set hasOtherThanMissingReturnItem flag on OrderTransfer if the given order has at least one refund entry
     * identified by the sales order id with a status other than missing
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws AmbiguousComparisonException
     */
    public function hydrateSalesOrderOtherThanMissingReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer;
}
