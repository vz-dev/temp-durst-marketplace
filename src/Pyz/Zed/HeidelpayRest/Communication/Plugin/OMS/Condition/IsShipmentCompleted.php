<?php
/**
 * Durst - project - IsShipmentCompleted.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 16:44
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsShipmentCompleted
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 */
class IsShipmentCompleted extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'HeidelpayRest/IsShipmentCompleted';

    /**
     * @inheritDoc
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderItem->getFkSalesOrder());

        return $this
            ->getFacade()
            ->isShipmentCompleted($orderTransfer);
    }
}
