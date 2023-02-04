<?php

namespace Pyz\Zed\Oms\Business\Util;

use DateTime;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Business\Util\OrderItemMatrix as SprykerOrderItemMatrix;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @property OmsConfig $config
 */
class OrderItemMatrix extends SprykerOrderItemMatrix
{
    const RECENT_ORDERS_MAX_AGE = '-1 year';

    /**
     * @return ObjectCollection|SpyOmsOrderProcess[]
     *
     * @throws AmbiguousComparisonException
     */
    protected function getActiveWholesaleProcessesWithRecentOrders(): ObjectCollection
    {
        $activeProcesses = $this->config->getActiveProcesses();

        $activeWholesaleProcesses = array_filter($activeProcesses, function ($process) {
            return $process !== $this->config->getRetailProcessName();
        });

        return $this
            ->queryContainer
            ->getActiveProcesses($activeWholesaleProcesses)
            ->useItemQuery(null, Criteria::INNER_JOIN)
                ->useOrderQuery()
                    ->filterByCreatedAt(
                        new DateTime(static::RECENT_ORDERS_MAX_AGE),
                        Criteria::GREATER_EQUAL
                    )
                ->endUse()
            ->endUse()
            ->groupByIdOmsOrderProcess()
            ->find();
    }

    /**
     * @return array
     *
     * @throws AmbiguousComparisonException
     */
    protected function getProcesses(): array
    {
        $activeWholesaleProcesses = $this->getActiveWholesaleProcessesWithRecentOrders();

        $processes = [];

        foreach ($activeWholesaleProcesses as $process) {
            $processes[$process->getIdOmsOrderProcess()] = $process->getName();
        }

        asort($processes);

        return $processes;
    }
}
