<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * @method OmsCommunicationFactory getFactory()
 */
trait GraphmastersTrait
{
    /**
     * @param SpySalesOrder $orderEntity
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function markGraphmastersOrderCancelled(SpySalesOrder $orderEntity): void
    {
        $graphMastersFacade = $this
            ->getFactory()
            ->getGraphMastersFacade();

        if ($graphMastersFacade->doesBranchUseGraphmasters($orderEntity->getFkBranch())) {
            $graphMastersFacade->markOrderCancelledByReference($orderEntity->getOrderReference());
        }
    }
}
