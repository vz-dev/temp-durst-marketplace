<?php
/**
 * Durst - project - IsShipmentFinalizePendingOrCoreTimeout.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.08.20
 * Time: 09:30
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsShipmentFinalizePendingOrCoreTimeout
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class IsShipmentFinalizePendingOrCoreTimeout extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsShipmentFinalizePendingOrCoreTimeout';

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

        return $this
            ->getFacade()
            ->isShipmentPendingOrRecoverableError(
                $orderTransfer,
                [
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE
                ]
            );
    }
}
