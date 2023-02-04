<?php

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * @method HeidelpayRestCommunicationFactory getFactory()
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
