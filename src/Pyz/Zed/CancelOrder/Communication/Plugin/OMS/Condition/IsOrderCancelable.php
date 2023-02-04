<?php
/**
 * Durst - project - IsOrderCancelable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.09.21
 * Time: 15:53
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition;

use DateTime;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsOrderCancelable
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition
 *
 * @method CancelOrderFacadeInterface getFacade()
 * @method CancelOrderCommunicationFactory getFactory()
 * @method CancelOrderConfig getConfig()
 */
class IsOrderCancelable extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'CancelOrder/IsOrderCancelable';

    /**
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

        $manualExpireDate = null;

        if ($orderTransfer->getCancelIssuer() === $this->getConfig()->getIssuerDriver()) {
            $manualExpireDate = new DateTime('tomorrow midnight');
        }

        $jwtTransfer = $this
            ->getFacade()
            ->generateToken(
                $orderItem
                    ->getFkSalesOrder(),
                $manualExpireDate
            );

        if ($jwtTransfer->getErrors()->count() > 0) {
            return false;
        }

        return $this
            ->getFacade()
            ->isValid(
                $jwtTransfer
            );
    }
}
