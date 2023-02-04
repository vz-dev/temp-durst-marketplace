<?php
/**
 * Durst - project - RefundAuthorization.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.09.21
 * Time: 16:04
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class RefundAuthorization
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class RefundAuthorization extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'refundAuthorization';
    public const NAME = 'CancelOrder/RefundAuthorization';
    public const STATE_NAME = 'refund cancel authorization';

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
        $this
            ->getFactory()
            ->getHeidelpayRestFacade()
            ->cancelAuthorization(
                $orderEntity
                    ->getIdSalesOrder()
            );

        return [];
    }
}
