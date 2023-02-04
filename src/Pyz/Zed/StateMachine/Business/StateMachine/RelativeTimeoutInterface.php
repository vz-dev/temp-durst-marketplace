<?php
/**
 * Durst - project - RelativeTimoutInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 18:11
 */

namespace Pyz\Zed\StateMachine\Business\StateMachine;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;

interface RelativeTimeoutInterface
{
    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function setNewTimeout(ProcessInterface $process, StateMachineItemTransfer $stateMachineItemTransfer);

    /**
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface $process
     * @param string $stateName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function dropOldTimeout(
        ProcessInterface $process,
        $stateName,
        StateMachineItemTransfer $stateMachineItemTransfer
    );
}