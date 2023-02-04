<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 12:44
 */

namespace Pyz\Zed\Refund\Business;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Refund\Business\RefundFacade as SprykerRefundFacade;

/**
 * Class RefundFacade
 * @package Pyz\Zed\Refund\Business
 * @method RefundBusinessFactory getFactory()
 */
class RefundFacade extends SprykerRefundFacade implements RefundFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderRefundInformation(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createRefundReader()
            ->hydrateSalesOrderRefundInformation($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hydrateSalesOrderReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createReturnItemReader()
            ->hydrateSalesOrderReturnItemFlag($orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int[] $salesOrderIds
     * @param bool $excludeMissingOrderItems
     * @return RefundTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getSalesOrderRefundsBySalesOrderIds(
        array $salesOrderIds,
        bool $excludeMissingOrderItems = false
    ): array {
        return $this
            ->getFactory()
            ->createRefundReader()
            ->getRefundsBySalesOrderIds($salesOrderIds, $excludeMissingOrderItems);
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws AmbiguousComparisonException
     */
    public function hydrateSalesOrderOtherThanMissingReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createReturnItemReader()
            ->hydrateSalesOrderOtherThanMissingReturnItemFlag($orderTransfer);
    }
}
