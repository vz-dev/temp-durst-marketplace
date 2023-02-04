<?php
/**
 * Durst - project - IsHeidelpayShipmentPendingOrCoreTimeout.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.08.20
 * Time: 09:22
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsHeidelpayShipmentPendingOrCoreTimeout
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class IsHeidelpayShipmentPendingOrCoreTimeout extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsHeidelpayShipmentPendingOrCoreTimeout';

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     * @return bool
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
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                ]
            );
    }
}
