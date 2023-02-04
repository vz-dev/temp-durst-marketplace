<?php
/**
 * Durst - project - IntegraCancellation.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.09.21
 * Time: 15:40
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class IntegraCancellation
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class IntegraCancellation extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'cancelIntegra';
    public const NAME = 'CancelOrder/IntegraCancellation';
    public const STATE_NAME = 'integra cancel';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $branchUsesIntegra = $this
            ->getFactory()
            ->getIntegraFacade()
            ->doesBranchUseIntegra(
                $orderEntity
                    ->getFkBranch()
            );

        // branch does not use Integra, bail out
        if ($branchUsesIntegra !== true) {
            return [];
        }

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderEntity
                    ->getIdSalesOrder()
            );

        // is_exportable is still true, so the order has not been sent to the branch
        if ($orderEntity->getIsExportable() === true) {
            $orderTransfer
                ->setIsExportable(
                    false
                );
        }
        // is_exportable is false, means the branch knows about the order, we need to set is_closable
        else {
            $orderTransfer
                ->setIsClosable(
                    true
                );
        }

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateOrder(
                $orderTransfer,
                $orderEntity
                    ->getIdSalesOrder()
            );

        return [];
    }
}
