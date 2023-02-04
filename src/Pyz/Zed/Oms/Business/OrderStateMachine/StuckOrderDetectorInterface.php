<?php

namespace Pyz\Zed\Oms\Business\OrderStateMachine;

use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface StuckOrderDetectorInterface
{
    /**
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function detect(): void;
}
