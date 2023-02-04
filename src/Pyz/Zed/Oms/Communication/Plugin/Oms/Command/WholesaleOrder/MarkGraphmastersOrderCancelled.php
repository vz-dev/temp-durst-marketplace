<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * @method HeidelpayRestFacadeInterface getFacade()
 * @method HeidelpayRestCommunicationFactory getFactory()
 *
 */
class MarkGraphmastersOrderCancelled extends AbstractPlugin implements CommandByOrderInterface
{
    use GraphmastersTrait;

    public const NAME = 'WholesaleOrder/MarkGraphmastersOrderCancelled';

    /**
     * Command which is executed per order basis
     *
     * @api
     *
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     *
     * @return array
     *
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $this->markGraphmastersOrderCancelled($orderEntity);

        return [];
    }
}
