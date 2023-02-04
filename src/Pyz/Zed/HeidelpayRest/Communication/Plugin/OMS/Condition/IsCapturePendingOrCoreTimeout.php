<?php
/**
 * Durst - project - IsCapturePendingOrCoreTimeout.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.07.20
 * Time: 14:14
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsCapturePendingOrCoreTimeout
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class IsCapturePendingOrCoreTimeout extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsCapturePendingOrCoreTimeout';

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
            ->isCapturePendingOrRecoverableError(
                $orderTransfer,
                [
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                ]
            );
    }
}
