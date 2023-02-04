<?php
/**
 * Durst - project - IsDamaged.phpnitial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-08-28
 * Time: 04:23
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsDamaged
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder
 * @method \Pyz\Zed\Oms\Business\OmsFacadeInterface getFacade()
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 */
class IsDamaged extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'Wholesale/IsDamaged';

    /**
     * @param SpySalesOrderItem $orderItem
     *
     * @return bool
     * @api
     *
     * @throws PropelException
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        return $orderItem->getDeliveryStatus() === SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DAMAGED;
    }
}
