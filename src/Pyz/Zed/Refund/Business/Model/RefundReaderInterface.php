<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 12:59
 */

namespace Pyz\Zed\Refund\Business\Model;


use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface RefundReaderInterface
{
    /**
     * Get all refunds for a given order
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderRefundInformation(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * returns all refund transfers for the sales order with the given id,
     * optionally excluding those for order items that have been marked missing
     *
     * @param int[] $salesOrderIds
     * @param bool $excludeMissingOrderItems
     * @return RefundTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getRefundsBySalesOrderIds(array $salesOrderIds, bool $excludeMissingOrderItems = false): array;
}
