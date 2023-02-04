<?php
/**
 * Durst - project - UtcDateTimeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\StateMachine\Business\StateMachine;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;

/**
 * Interface UtcDateTimeInterface
 * @package Pyz\Zed\StateMachine\Business\StateMachine
 */
interface UtcDateTimeInterface
{
    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return void
     */
    public function setNewTimeout(
        ProcessInterface $process,
        StateMachineItemTransfer $itemTransfer
    ): void;

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $itemTransfer
     * @return void
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        string $stateName,
        StateMachineItemTransfer $itemTransfer
    ): void;
}
