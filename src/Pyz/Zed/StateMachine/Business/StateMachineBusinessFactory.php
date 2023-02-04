<?php
/**
 * Durst - project - StateMachineBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 18:04
 */

namespace Pyz\Zed\StateMachine\Business;

use Pyz\Zed\StateMachine\Business\Graph\Drawer;
use Pyz\Zed\StateMachine\Business\Process\Event;
use Pyz\Zed\StateMachine\Business\Process\State;
use Pyz\Zed\StateMachine\Business\StateMachine\Builder;
use Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeout;
use Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface;
use Pyz\Zed\StateMachine\Business\StateMachine\StateUpdater;
use Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTime;
use Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineBusinessFactory as SprykerStateMachineBusinessFactory;
use Spryker\Zed\StateMachine\StateMachineConfig;

class StateMachineBusinessFactory extends SprykerStateMachineBusinessFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\EventInterface
     */
    public function createProcessEvent()
    {
        return new Event();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\StateInterface
     */
    public function createProcessState()
    {
        return new State();
    }

    /**
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface
     */
    public function createStateMachineRelativeTimeout(): RelativeTimeoutInterface
    {
        return new RelativeTimeout(
            $this->createStateMachinePersistence(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface
     */
    public function createStateMachineUtcDateTime(): UtcDateTimeInterface
    {
        return new UtcDateTime(
            $this->createStateMachinePersistence(),
            $this->getQueryContainer()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\StateUpdater|\Spryker\Zed\StateMachine\Business\StateMachine\StateUpdaterInterface
     */
    public function createStateUpdater()
    {
        return new StateUpdater(
            $this->createStateMachineTimeout(),
            $this->createStateMachineRelativeTimeout(),
            $this->createStateMachineUtcDateTime(),
            $this->createHandlerResolver(),
            $this->createStateMachinePersistence(),
            $this->getQueryContainer()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\Builder|\Spryker\Zed\StateMachine\Business\StateMachine\BuilderInterface
     */
    public function createStateMachineBuilder()
    {
        return new Builder(
            $this->createProcessEvent(),
            $this->createProcessState(),
            $this->createProcessTransition(),
            $this->createProcessProcess(),
            $this->getConfig()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $stateMachineName
     *
     * @return \Pyz\Zed\StateMachine\Business\Graph\Drawer|\Spryker\Zed\StateMachine\Business\Graph\DrawerInterface
     */
    public function createGraphDrawer($stateMachineName)
    {
        return new Drawer(
            $this->getGraph()->init(StateMachineConfig::GRAPH_NAME, $this->getConfig()->getGraphDefaults(), true, false),
            $this->createHandlerResolver()->get($stateMachineName)
        );
    }
}
