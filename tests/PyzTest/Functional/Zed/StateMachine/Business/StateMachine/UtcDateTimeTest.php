<?php
namespace PyzTest\Functional\Zed\StateMachine\Business\StateMachine;

use DateTime;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeNotInFutureException;
use Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException;
use Pyz\Zed\StateMachine\Business\Process\Event;
use Pyz\Zed\StateMachine\Business\Process\State;
use Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTime;
use Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface;
use PyzTest\Functional\Zed\StateMachine\Mocks\StateMachineMocks;
use Spryker\Zed\StateMachine\Business\Process\Process;
use Spryker\Zed\StateMachine\Business\Process\Transition;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class UtcDateTimeTest extends StateMachineMocks
{
    protected const UTC_DATETIME = '\Orm\Zed\Tour\Persistence\DstConcreteTour.preparationStart';
    protected const STATE_WITH_UTC_DATETIME = 'State with UTC DateTime';
    protected const UTC_DATETIME_IDENTIFIER = 1;

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetUtcDateTimeShouldStoreNewUtcDateTimeTimeout(): void
    {
        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->once()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $stateMachinePersistenceMock
            ->expects(
                $this->once()
            )
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $idConcreteTour = $this
            ->createConcreteTour();

        $utcDateTime
            ->setNewTimeout(
                $this->createProcess(static::UTC_DATETIME),
                $this->createStateMachineItemTransfer($idConcreteTour)
            );
    }

    /**
     * @return void
     */
    public function testDropOldUtcDateTimeTimeoutShouldRemoveExpiredTimeoutsFromPersistence(): void
    {
        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->once()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $utcDateTime
            ->dropOldTimeout(
                $this->createProcess(static::UTC_DATETIME),
                static::STATE_WITH_UTC_DATETIME,
                $this->createStateMachineItemTransfer(static::UTC_DATETIME_IDENTIFIER)
            );
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testSetUtcDateTimeInPastShouldThrowUtcDateTimeNotInFutureException(): void
    {
        $this
            ->expectException(UtcDateTimeNotInFutureException::class);

        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $utcDateTime
            ->setNewTimeout(
                $this->createProcess(static::UTC_DATETIME),
                $this->createStateMachineItemTransfer(static::UTC_DATETIME_IDENTIFIER)
            );
    }

    /**
     * @return void
     */
    public function testSetUtcDateTimeWithMissingColumnShouldThrowUtcDateTimeParameterMismatchException(): void
    {
        $this
            ->expectException(UtcDateTimeParameterMismatchException::class);

        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $utcDateTime
            ->setNewTimeout(
                $this->createProcess('\Orm\Zed\Tour\Persistence\DstConcreteTour'),
                $this->createStateMachineItemTransfer(static::UTC_DATETIME_IDENTIFIER)
            );
    }

    /**
     * @return void
     */
    public function testSetUtcDateTimeWithNonExistingClassShouldThrowUtcDateTimeParameterMismatchException(): void
    {
        $this
            ->expectException(UtcDateTimeParameterMismatchException::class);

        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $utcDateTime
            ->setNewTimeout(
                $this->createProcess('This\Class\Does\Not\Exist'),
                $this->createStateMachineItemTransfer(static::UTC_DATETIME_IDENTIFIER)
            );
    }

    /**
     * @return void
     */
    public function testSetUtcDateTimeWithNonDateTimeColumnShouldThrowUtcDateTimeParameterMismatchException(): void
    {
        $this
            ->expectException(UtcDateTimeParameterMismatchException::class);

        $stateMachinePersistenceMock = $this
            ->createPersistenceMock();
        $queryContainerMock = $this
            ->createStateMachineQueryContainerMock();

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('dropTimeoutByItem')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class)
            );

        $stateMachinePersistenceMock
            ->expects(
                $this->never()
            )
            ->method('saveStateMachineItemTimeout')
            ->with(
                $this->isInstanceOf(StateMachineItemTransfer::class),
                $this->isInstanceOf(DateTime::class),
                $this->isType('string')
            );

        $utcDateTime = $this
            ->createUtcDateTime(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $utcDateTime
            ->setNewTimeout(
                $this->createProcess('\Orm\Zed\Tour\Persistence\DstConcreteTour.prepTime'),
                $this->createStateMachineItemTransfer(static::UTC_DATETIME_IDENTIFIER)
            );
    }

    /**
     * @param string $utcDateTime
     * @return \Spryker\Zed\StateMachine\Business\Process\Process
     */
    protected function createProcess(string $utcDateTime): Process
    {
        $process = new Process();

        $outgoingTransitions = new Transition();
        $event = new Event();
        $event
            ->setName('UTC DateTime Event');
        $event
            ->setUtcDateTime($utcDateTime);
        $outgoingTransitions
            ->setEvent($event);

        $state = new State();
        $state
            ->setName(static::STATE_WITH_UTC_DATETIME);
        $state
            ->addOutgoingTransition($outgoingTransitions);

        $process
            ->addState($state);

        return $process;
    }

    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(int $idConcreteTour): StateMachineItemTransfer
    {
        $stateMachineItemTransfer = (new StateMachineItemTransfer())
            ->setStateName(static::STATE_WITH_UTC_DATETIME)
            ->setIdentifier($idConcreteTour);

        return $stateMachineItemTransfer;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $persistence
     * @param \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface $stateMachineQueryContainer
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface
     */
    protected function createUtcDateTime(
        PersistenceInterface $persistence,
        StateMachineQueryContainerInterface $stateMachineQueryContainer
    ): UtcDateTimeInterface
    {
        if ($persistence === null) {
            $persistence = $this
                ->createPersistenceMock();
        }

        if ($stateMachineQueryContainer === null) {
            $stateMachineQueryContainer = $this
                ->createStateMachineQueryContainerMock();
        }

        return new UtcDateTime(
            $persistence,
            $stateMachineQueryContainer
        );
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function createConcreteTour(): int
    {
        $currentDate = new DateTime('+1day');

        $concreteTourEntity = (new DstConcreteTour())
            ->setTourReference('DE-TOUR-1001')
            ->setFkBranch(8)
            ->setFkAbstractTour(1)
            ->setDate($currentDate)
            ->setComment('Test tour for UTC DateTime')
            ->setExportable(false)
            ->setIsCommissioned(false)
            ->setPreparationStart($currentDate)
            ->setPrepTime(10);

        $concreteTourEntity
            ->save();

        return $concreteTourEntity
            ->getIdConcreteTour();
    }
}
