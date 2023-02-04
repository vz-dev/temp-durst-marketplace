<?php
/**
 * Durst - project - RelativeTimeout.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 18:11
 */

namespace Pyz\Zed\StateMachine\Business\StateMachine;

use DateInterval;
use DateTime;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException;
use Pyz\Zed\StateMachine\Business\Process\EventInterface;
use Spryker\Zed\StateMachine\Business\Exception\StateMachineException;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class RelativeTimeout implements RelativeTimeoutInterface
{
    /**
     * @var \Spryker\Zed\StateMachine\Business\Process\StateInterface[]
     */
    protected $stateIdToModelBuffer = [];

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * RelativeTimeout constructor.
     *
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface $queryContainer
     */
    public function __construct(
        PersistenceInterface $stateMachinePersistence,
        StateMachineQueryContainerInterface $queryContainer
    ) {
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function setNewTimeout(ProcessInterface $process, StateMachineItemTransfer $stateMachineItemTransfer)
    {
        /** @var \Pyz\Zed\StateMachine\Business\Process\StateInterface $targetState */
        $targetState = $this->getStateFromProcess($stateMachineItemTransfer->getStateName(), $process);
        if (!$targetState->hasRelativeTimeoutEvent()) {
            return;
        }

        $events = $targetState->getRelativeTimeoutEvents();
        $handledEvents = [];
        $currentTime = new DateTime('now');
        /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
        foreach ($events as $event) {
            if (in_array($event->getName(), $handledEvents)) {
                continue;
            }

            $handledEvents[] = $event->getName();
            $timeoutDate = $this->calculateTimeoutDateFromEvent($currentTime, $event, $stateMachineItemTransfer);

            $this->stateMachinePersistence->dropTimeoutByItem($stateMachineItemTransfer);

            $this->stateMachinePersistence->saveStateMachineItemTimeout($stateMachineItemTransfer, $timeoutDate, $event->getName());
        }
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        $stateName,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        /** @var \Pyz\Zed\StateMachine\Business\Process\StateInterface $sourceState */
        $sourceState = $this->getStateFromProcess($stateName, $process);

        if ($sourceState->hasRelativeTimeoutEvent()) {
            $this->stateMachinePersistence->dropTimeoutByItem($stateMachineItemTransfer);
        }
    }

    /**
     * @param \DateTime $currentTime
     * @param \Pyz\Zed\StateMachine\Business\Process\EventInterface $event
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return \DateTime
     */
    protected function calculateTimeoutDateFromEvent(DateTime $currentTime, EventInterface $event, StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $timeout = $event->getRelativeTimeout();
        $interval = $this
            ->getTimeoutFromEntity($timeout, $stateMachineItemTransfer);

        $addedTime = $currentTime
            ->add($interval)
            ->setTime(
                $currentTime->format('H'),
                $currentTime->format('i'),
                0
            );

        return $addedTime;
    }

    /**
     * @param string $relativeTimeout
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return \DateInterval
     * @throws \Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException
     */
    protected function getTimeoutFromEntity(string $relativeTimeout, StateMachineItemTransfer $stateMachineItemTransfer): DateInterval
    {
        $timeoutInUnit = $this->parseRelativeTimeout($relativeTimeout, $stateMachineItemTransfer);

        return DateInterval::createFromDateString($timeoutInUnit . ' min');
    }

    /**
     * @param string $relativeTimeout
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException
     *
     * @return int
     */
    protected function parseRelativeTimeout(string $relativeTimeout, StateMachineItemTransfer $stateMachineItemTransfer): int
    {
        $relativeTimeoutArray = explode('.', $relativeTimeout);

        if (count($relativeTimeoutArray) < 2) {
            throw new InvalidRelativeTimeoutException($relativeTimeout);
        }

        $tableName = array_shift($relativeTimeoutArray);
        $columnNames = $relativeTimeoutArray;

        $queryClass = $tableName . 'Query';
        if (class_exists($queryClass) !== true){
            throw InvalidRelativeTimeoutException::createQueryNotFound($queryClass);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $entity = $queryClass::create()
            ->findPk($stateMachineItemTransfer->getIdentifier());

        if ($entity === null) {
            throw InvalidRelativeTimeoutException::createEntityNotFound(
                $queryClass,
                $stateMachineItemTransfer->getIdentifier()
            );
        }

        $timeout = 0;
        $counter = 0;

        foreach ($columnNames as $columnName) {
            $getMethod = 'get' . ucfirst($columnName);

            $counter++;

            if ($counter < count($columnNames)) {
                $entity = $entity
                    ->$getMethod();

                continue;
            }

            $timeout = $entity
                ->$getMethod();
        }

        if ($timeout === null || $timeout < 0) {
            $timeout = 0;
        }

        return $timeout;
    }

    /**
     * @param string $stateName
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     *
     * @return \Spryker\Zed\StateMachine\Business\Process\StateInterface
     */
    protected function getStateFromProcess($stateName, ProcessInterface $process)
    {
        if (!isset($this->stateIdToModelBuffer[$stateName])) {
            $this->stateIdToModelBuffer[$stateName] = $process->getStateFromAllProcesses($stateName);
        }

        return $this->stateIdToModelBuffer[$stateName];
    }
}
