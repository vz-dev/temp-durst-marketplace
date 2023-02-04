<?php

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

class MarkCancelDeliveryStatus extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'markCancelDeliveryStatus';
    public const NAME = 'CancelOrder/MarkCancelDeliveryStatus';
    public const STATE_NAME = 'canceled driver';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        foreach ($orderItems as $orderItem) {
            $this
                ->getFactory()
                ->getSalesFacade()
                ->setOrderItemDeliveryStatus(
                    $orderItem->getIdSalesOrderItem(),
                    SalesConstants::ORDER_ITEM_DELIVERY_STATUS_CANCELLED
                );
        }

        return [];
    }
}
