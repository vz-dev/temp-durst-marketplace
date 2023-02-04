<?php
/**
 * Durst - project - CreateInvoice.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.07.18
 * Time: 15:25
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class SendInvoice
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method OmsFacadeInterface getFacade()
 * @method OmsCommunicationFactory getFactory()
 */
class CreateInvoice extends AbstractCommand implements CommandByOrderInterface
{
    /**
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
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $this
            ->getFacade()
            ->createInvoice($orderEntity->getIdSalesOrder());

        return $data->getArrayCopy();
    }
}
