<?php
namespace PyzTest\Functional\Zed\StateMachine\Business\StateMachine;

use DateTime;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException;
use Pyz\Zed\StateMachine\Business\Process\Event;
use Pyz\Zed\StateMachine\Business\Process\State;
use Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeout;
use Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface;
use PyzTest\Functional\Zed\StateMachine\Mocks\StateMachineMocks;
use Spryker\Zed\StateMachine\Business\Process\Process;
use Spryker\Zed\StateMachine\Business\Process\Transition;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class RelativeTimeoutTest extends StateMachineMocks
{
    protected const RELATIVE_TIMEOUT = '\Orm\Zed\Tour\Persistence\DstConcreteTour.prepTime';
    protected const STATE_WITH_RELATIVE_TIMEOUT = 'State with relative timeout';
    protected const RELATIVE_TIMEOUT_IDENTIFIER = 1;

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testSetRelativeTimeoutShouldStoreNewRelativeTimeout(): void
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

        $relativeTimeout = $this
            ->createRelativeTimeout(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $relativeTimeout
            ->setNewTimeout(
                $this->createProcess(static::RELATIVE_TIMEOUT),
                $this->createStateMachineItemTransfer(static::RELATIVE_TIMEOUT_IDENTIFIER)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testDropOldRelativeTimeoutShouldRemoveExpiredTimeoutsFromPersistence(): void
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

        $relativeTimeout = $this
            ->createRelativeTimeout(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $relativeTimeout
            ->dropOldTimeout(
                $this->createProcess(static::RELATIVE_TIMEOUT),
                static::STATE_WITH_RELATIVE_TIMEOUT,
                $this->createStateMachineItemTransfer(static::RELATIVE_TIMEOUT_IDENTIFIER)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testSetRelativeTimeoutWithoutColumnsShouldThrowInvalidRelativeTimeoutException(): void
    {
        $this
            ->expectException(InvalidRelativeTimeoutException::class);

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

        $relativeTimeout = $this
            ->createRelativeTimeout(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $relativeTimeout
            ->setNewTimeout(
                $this->createProcess('\Orm\Zed\Tour\Persistence\DstConcreteTour'),
                $this->createStateMachineItemTransfer(static::RELATIVE_TIMEOUT_IDENTIFIER)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testSetRelativeTimeoutFromUnknownClassShouldThrowInvalidRelativeTimeoutException(): void
    {
        $this
            ->expectException(InvalidRelativeTimeoutException::class);

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

        $relativeTimeout = $this
            ->createRelativeTimeout(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $relativeTimeout
            ->setNewTimeout(
                $this->createProcess('This\Class\Does\Not\Exist'),
                $this->createStateMachineItemTransfer(static::RELATIVE_TIMEOUT_IDENTIFIER)
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function testSetRelativeTimeoutWithNonExistingConcreteTourShouldThrowPropelException(): void
    {
        $this
            ->expectException(PropelException::class);

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

        $relativeTimeout = $this
            ->createRelativeTimeout(
                $stateMachinePersistenceMock,
                $queryContainerMock
            );

        $relativeTimeout
            ->setNewTimeout(
                $this->createProcess(static::RELATIVE_TIMEOUT),
                $this->createStateMachineItemTransfer(12345654321)
            );
    }

    /**
     * @param string $relativeTimeout
     * @return \Spryker\Zed\StateMachine\Business\Process\Process
     */
    protected function createProcess(string $relativeTimeout): Process
    {
        $process = new Process();

        $outgoingTransitions = new Transition();
        $event = new Event();
        $event
            ->setName('Relative Timeout Event');
        $event
            ->setRelativeTimeout($relativeTimeout);
        $outgoingTransitions
            ->setEvent($event);

        $state = new State();
        $state
            ->setName(static::STATE_WITH_RELATIVE_TIMEOUT);
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
            ->setStateName(static::STATE_WITH_RELATIVE_TIMEOUT)
            ->setIdentifier($idConcreteTour);

        return $stateMachineItemTransfer;
    }

    /**
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $persistence
     * @param \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface $stateMachineQueryContainer
     * @return \Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface
     */
    protected function createRelativeTimeout(
        PersistenceInterface $persistence,
        StateMachineQueryContainerInterface $stateMachineQueryContainer
    ): RelativeTimeoutInterface
    {
        if ($persistence === null) {
            $persistence = $this
                ->createPersistenceMock();
        }

        if ($stateMachineQueryContainer === null) {
            $stateMachineQueryContainer = $this
                ->createStateMachineQueryContainerMock();
        }

        return new RelativeTimeout(
            $persistence,
            $stateMachineQueryContainer
        );
    }
}
