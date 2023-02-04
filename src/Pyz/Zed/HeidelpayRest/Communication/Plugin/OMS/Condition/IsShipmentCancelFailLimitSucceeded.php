<?php
/**
 * Durst - project - IsShipmentCancelFailLimitSucceeded.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.08.20
 * Time: 09:27
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsShipmentCancelFailLimitSucceeded
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig getConfig()
 */
class IsShipmentCancelFailLimitSucceeded extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsShipmentCancelFailLimitSucceeded';

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderItem
                    ->getFkSalesOrder()
            );

        return (
            $orderTransfer->getOmsRetryCounter() > $this->getConfig()->getSalesOrderRetryCounter()
        );
    }
}
