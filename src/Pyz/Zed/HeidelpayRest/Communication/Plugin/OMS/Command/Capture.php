<?php
/**
 * Durst - project - Capture.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 14:46
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class Capture
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 */
class Capture extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'HeidelpayRest/Capture';

    /**
     *
     * {@inheritdoc}
     *
     * Command which is executed per order basis
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    )
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        $this
            ->getFacade()
            ->capturePaymentForOrder($orderTransfer);

        return [];
    }
}