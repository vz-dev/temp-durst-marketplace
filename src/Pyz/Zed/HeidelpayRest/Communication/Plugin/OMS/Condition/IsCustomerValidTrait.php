<?php

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

trait IsCustomerValidTrait
{
    /**
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    protected function isCustomerValid(SpySalesOrderItem $orderItem): bool
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderItem->getFkSalesOrder());

        return $this
            ->getFacade()
            ->isCustomerValid($orderTransfer);
    }
}
