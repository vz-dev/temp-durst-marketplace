<?php

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition;

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
class IsCaptureNotApprovedAndBranchUsesGraphmasters extends AbstractPlugin implements ConditionInterface
{
    use IsCaptureApprovedTrait;

    public const NAME = 'HeidelpayRest/IsCaptureNotApprovedAndBranchUsesGraphmasters';

    /**
     * @inheritDoc
     *
     * @param SpySalesOrderItem $orderItem
     * @return bool
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        if ($this->isCaptureApproved($orderItem)) {
            return false;
        }

        return $this
            ->getFactory()
            ->getGraphMastersFacade()
            ->doesBranchUseGraphmasters($orderItem->getOrder()->getFkBranch());
    }
}
