<?php
/**
 * Durst - project - NeedsRefund.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.03.19
 * Time: 11:20
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\Oms\Business\OmsFacade;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class NeedsRefund
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 * @method OmsFacade getFacade()
 */
class NeedsRefund extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'WholesaleOrder/NeedsRefund';

    /**
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $idOrder = $orderItem->getFkSalesOrder();
        $depositFacade = $this
            ->getFactory()
            ->getDepositFacade();

        return ($this->hasOrderRefunds($idOrder) ||
            $depositFacade->hasOrderDepositReturns($idOrder));
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    protected function hasOrderRefunds(int $idSalesOrder): bool
    {
        $refunds = $this
            ->getFactory()
            ->getRefundFacade()
            ->getSalesOrderRefundsBySalesOrderIds([$idSalesOrder]);

        return (count($refunds) > 0);
    }
}
