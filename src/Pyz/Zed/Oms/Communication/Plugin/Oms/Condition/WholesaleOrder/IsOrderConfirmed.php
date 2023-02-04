<?php
/**
 * Durst - project - IsOrderConfirmed.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.07.20
 * Time: 09:15
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsOrderConfirmed
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 */
class IsOrderConfirmed extends AbstractPlugin implements ConditionInterface
{
    use IsOrderConfirmedTrait;

    public const NAME = 'WholesaleOrder/IsOrderConfirmed';

    /**
     * {@inheritDoc}
     *
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return true;
        return $this->isOrderConfirmed($orderItem);
    }
}
