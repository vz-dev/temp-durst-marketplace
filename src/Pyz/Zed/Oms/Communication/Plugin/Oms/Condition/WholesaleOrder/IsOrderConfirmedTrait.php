<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

trait IsOrderConfirmedTrait
{
    /**
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function isOrderConfirmed(SpySalesOrderItem $orderItem): bool
    {
        $salesOrderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderItem
                    ->getFkSalesOrder()
            );

        return ($salesOrderTransfer->getConfirmedAt() !== null);
    }
}
