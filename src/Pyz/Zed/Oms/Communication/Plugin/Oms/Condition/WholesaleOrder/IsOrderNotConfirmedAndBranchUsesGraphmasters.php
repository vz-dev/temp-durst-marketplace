<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * @method HeidelpayRestCommunicationFactory getFactory()
 * @method HeidelpayRestFacadeInterface getFacade()
 */
class IsOrderNotConfirmedAndBranchUsesGraphmasters extends AbstractPlugin implements ConditionInterface
{
    use IsOrderConfirmedTrait;

    public const NAME = 'WholesaleOrder/IsOrderNotConfirmedAndBranchUsesGraphmasters';

    /**
     * @inheritDoc
     *
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        if ($this->isOrderConfirmed($orderItem) === true) {
            return false;
        }

        return $this
            ->getFactory()
            ->getGraphMastersFacade()
            ->doesBranchUseGraphmasters($orderItem->getOrder()->getFkBranch());
    }
}
