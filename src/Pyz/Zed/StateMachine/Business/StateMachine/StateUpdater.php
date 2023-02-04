<?php
/**
 * Durst - project - StateUpdater.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-21
 * Time: 10:32
 */

namespace Pyz\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\StateUpdater as SprykerStateUpdater;
use Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class StateUpdater extends SprykerStateUpdater
{
    /**
     * @var \Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface
     */
    protected $relativeTimeout;

    /**
     * @var \Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface
     */
    protected $utcDateTime;

    /**
     *
     * StateUpdater constructor.
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface $timeout
     * @param \Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface $relativeTimeout
     * @param \Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface $utcDateTime
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface $stateMachineQueryContainer
     */
    public function __construct(
        TimeoutInterface $timeout,
        RelativeTimeoutInterface $relativeTimeout,
        UtcDateTimeInterface $utcDateTime,
        HandlerResolverInterface $stateMachineHandlerResolver,
        PersistenceInterface $stateMachinePersistence,
        StateMachineQueryContainerInterface $stateMachineQueryContainer
    )
    {
        parent::__construct($timeout,
            $stateMachineHandlerResolver,
            $stateMachinePersistence,
            $stateMachineQueryContainer);

        $this->relativeTimeout = $relativeTimeout;
        $this->utcDateTime = $utcDateTime;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param string $sourceState
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function updateTimeouts(
        ProcessInterface $process,
        $sourceState,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        $this->utcDateTime->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
        $this->utcDateTime->setNewTimeout($process, $stateMachineItemTransfer);
        $this->timeout->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
        $this->timeout->setNewTimeout($process, $stateMachineItemTransfer);
        $this->relativeTimeout->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
        $this->relativeTimeout->setNewTimeout($process, $stateMachineItemTransfer);
    }
}
