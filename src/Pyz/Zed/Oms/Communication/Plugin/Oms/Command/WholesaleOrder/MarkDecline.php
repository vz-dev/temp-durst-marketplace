<?php
/**
 * Durst - project - MarkDecline.phpInitial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-15
 * Time: 11:38
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByItemInterface;

/**
 * Class CustomerDecline
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 * @method \Pyz\Zed\Oms\Business\OmsFacadeInterface getFacade()
 */
class MarkDecline extends AbstractCommand implements CommandByItemInterface
{
    public const EVENT_ID = 'markDecline';
    public const NAME = 'WholesaleOrder/MarkDecline';
    public const STATE_NAME = 'mark declined';

    /**
     * Command which is executed per order item basis
     *
     * @param SpySalesOrderItem $orderItem
     * @param ReadOnlyArrayObject $data
     *
     * @return array
     * @api
     *
     * @throws PropelException
     */
    public function run(SpySalesOrderItem $orderItem, ReadOnlyArrayObject $data)
    {
        $this
            ->getFactory()
            ->getSalesFacade()
            ->setOrderItemDeliveryStatus(
                $orderItem->getIdSalesOrderItem(),
                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DECLINED
            );

        return [];
    }
}
