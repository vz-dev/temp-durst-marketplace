<?php
/**
 * Durst - project - CaptureCancel.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.08.20
 * Time: 09:39
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class CaptureCancel
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class CaptureCancel extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'HeidelpayRest/CaptureCancel';

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
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderEntity
                    ->getIdSalesOrder()
            );

        $this
            ->getFacade()
            ->cancelRemainingAmountForOrder(
                $orderTransfer
            );

        return [];
    }
}
