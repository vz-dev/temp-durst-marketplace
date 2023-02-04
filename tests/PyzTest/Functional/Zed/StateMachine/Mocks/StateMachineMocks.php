<?php
/**
 * Durst - project - StateMachineMocks.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-08
 * Time: 11:11
 */

namespace PyzTest\Functional\Zed\StateMachine\Mocks;


use Codeception\Test\Unit;
use Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface;
use Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface;
use Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class StateMachineMocks extends Unit
{

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected function createPersistenceMock(): PersistenceInterface
    {
        $persistenceMock = $this
            ->getMockBuilder(PersistenceInterface::class)
            ->getMock();

        return $persistenceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface
     */
    protected function createStateMachineQueryContainerMock(): StateMachineQueryContainerInterface
    {
        $queryContainerMock = $this
            ->getMockBuilder(StateMachineQueryContainerInterface::class)
            ->getMock();

        return $queryContainerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Pyz\Zed\StateMachine\Business\StateMachine\RelativeTimeoutInterface
     */
    protected function createRelativeTimeoutMock(): RelativeTimeoutInterface
    {
        $relativeTimeoutMock = $this
            ->getMockBuilder(RelativeTimeoutInterface::class)
            ->getMock();

        return $relativeTimeoutMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Pyz\Zed\StateMachine\Business\StateMachine\UtcDateTimeInterface
     */
    protected function createUtcDateTimeMock(): UtcDateTimeInterface
    {
        $utcDateTimeMock = $this
            ->getMockBuilder(UtcDateTimeInterface::class)
            ->getMock();

        return $utcDateTimeMock;
    }
}
