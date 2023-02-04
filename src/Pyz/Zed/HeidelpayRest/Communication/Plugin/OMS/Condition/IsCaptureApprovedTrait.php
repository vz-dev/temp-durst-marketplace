<?php

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

trait IsCaptureApprovedTrait
{
    /**
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function isCaptureApproved(SpySalesOrderItem $orderItem): bool
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderItem->getFkSalesOrder());

        return $this
            ->getFacade()
            ->isCaptureApproved($orderTransfer);
    }
}
