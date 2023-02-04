<?php
/**
 * Durst - project - CreateInvoice.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 10:44
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class CreateInvoice
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class CreateInvoice extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'HeidelpayRest/CreateInvoice';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ) {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        return $this
            ->getFacade()
            ->createInvoice($orderTransfer);
    }
}
