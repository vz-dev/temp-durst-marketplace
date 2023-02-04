<?php
/**
 * Durst - project - UtcDateTime.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\StateMachine\Business\StateMachine;


use DateTime;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException;
use Pyz\Zed\StateMachine\Business\Process\EventInterface;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;
use Spryker\Zed\StateMachine\Business\Process\StateInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

/**
 * Class UtcDateTime
 * @package Pyz\Zed\StateMachine\Business\StateMachine
 */
class UtcDateTime implements UtcDateTimeInterface
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
     * UtcDateTime constructor.
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface $queryContainer
     */
    public function __construct(
        PersistenceInterface $stateMachinePersistence,
        StateMachineQueryContainerInterface $queryContainer
    )
    {
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return void
     * @throws \Exception
     */
    public function setNewTimeout(
        ProcessInterface $process,
        StateMachineItemTransfer $itemTransfer
    ): void
    {
        /** @var \Pyz\Zed\StateMachine\Business\Process\StateInterface $targetState */
        $targetState = $this
            ->getStateFromProcess(
                $itemTransfer->getStateName(),
                $process
            );

        if ($targetState->hasUtcDateTimeEvent() !== true) {
            return;
        }

        $events = $targetState
            ->getUtcDateTimeEvents();
        $handledEvents = [];

        /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
        foreach ($events as $event) {
            if (in_array($event->getName(), $handledEvents) === true) {
                continue;
            }

            $handledEvents[] = $event
                ->getName();

            $utcDateTime = $this
                ->getUtcDateTimeFromEvent(
                    $event,
                    $itemTransfer
                );

            $this
                ->stateMachinePersistence
                ->dropTimeoutByItem($itemTransfer);

            $this
                ->stateMachinePersistence
                ->saveStateMachineItemTimeout(
                    $itemTransfer,
                    $utcDateTime,
                    $event->getName()
                );
        }
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return void
     * @throws \Exception
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        string $stateName,
        StateMachineItemTransfer $itemTransfer
    ): void
    {
        /** @var \Pyz\Zed\StateMachine\Business\Process\StateInterface $sourceState */
        $sourceState = $this
            ->getStateFromProcess(
                $stateName,
                $process
            );

        if ($sourceState->hasUtcDateTimeEvent() === true) {
            $this
                ->stateMachinePersistence
                ->dropTimeoutByItem($itemTransfer);
        }
    }

    /**
     * @param string $stateName
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @return \Spryker\Zed\StateMachine\Business\Process\StateInterface
     * @throws \Exception
     */
    protected function getStateFromProcess(string $stateName, ProcessInterface $process): StateInterface
    {
        if (isset($this->stateIdToModelBuffer[$stateName]) !== true) {
            $this->stateIdToModelBuffer[$stateName] = $process
                ->getStateFromAllProcesses($stateName);
        }

        return $this->stateIdToModelBuffer[$stateName];
    }

    /**
     * @param \Pyz\Zed\StateMachine\Business\Process\EventInterface $event
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return \DateTime
     * @throws \Exception
     */
    protected function getUtcDateTimeFromEvent(
        EventInterface $event,
        StateMachineItemTransfer $itemTransfer
    ): DateTime
    {
        $timeoutColumn = $event
            ->getUtcDateTime();

        $utcDateTime = $this
            ->getDateTimeFromEntity(
                $timeoutColumn,
                $itemTransfer
            );

        $utcDateTime
            ->setTime(
                $utcDateTime->format('H'),
                $utcDateTime->format('i'),
                0
            );

        return $utcDateTime;
    }

    /**
     * @param string $timeoutColumn
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return \DateTime
     */
    protected function getDateTimeFromEntity(
        string $timeoutColumn,
        StateMachineItemTransfer $itemTransfer
    ): DateTime
    {
        $databaseTableAndColumn = explode('.', $timeoutColumn);

        if (count($databaseTableAndColumn) < 2) {
            throw UtcDateTimeParameterMismatchException::createParameterIsMissing($timeoutColumn);
        }

        $tableName = $databaseTableAndColumn[0];
        $columnName = $databaseTableAndColumn[1];

        $queryClass = sprintf(
            '%sQuery',
            $tableName
        );

        if (class_exists($queryClass) !== true) {
            throw UtcDateTimeParameterMismatchException::createQueryClassMissing($queryClass);
        }

        $getMethod = sprintf(
            'get%s',
            ucfirst($columnName)
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $entity = $queryClass::create()
            ->findPk($itemTransfer->getIdentifier());

        if ($entity === null) {
            throw UtcDateTimeParameterMismatchException::createEntityNotFound($itemTransfer->getIdentifier());
        }

        $value = $entity
            ->$getMethod();

        if (($value instanceof DateTime) !== true) {
            throw UtcDateTimeParameterMismatchException::createReturnValueNotADateTime($value);
        }

        return $value;
    }
}
