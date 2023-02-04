<?php
/**
 * Durst - project - IsIssuerDriver.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.09.21
 * Time: 15:03
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsIssuerDriver
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition
 *
 * @method CancelOrderCommunicationFactory getFactory()
 * @method CancelOrderConfig getConfig()
 */
class IsIssuerDriver extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'CancelOrder/IsIssuerDriver';

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function check(
        SpySalesOrderItem $orderItem
    ): bool
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderItem
                    ->getFkSalesOrder()
            );

        return ($orderTransfer->getCancelIssuer() === $this->getConfig()->getIssuerDriver());
    }
}
