<?php
/**
 * Durst - project - IsRefundApproved.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.01.19
 * Time: 12:41
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsRefundCompleted
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 */
class IsRefundCompleted extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsRefundCompleted';

    /**
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderItem->getFkSalesOrder());

        return $this
            ->getFacade()
            ->isRefundCompleted($orderTransfer);
    }
}
