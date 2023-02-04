<?php
/**
 * Durst - project - TourStateMachineBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Dependency\Facade;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;

interface TourToStateMachineBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer $stateMachineProcessTransfer
     * @param int $identifier
     * @return int
     */
    public function triggerForNewStateMachineItem(StateMachineProcessTransfer $stateMachineProcessTransfer, int $identifier): int;

    /**
     * @param string $eventName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return int
     */
    public function triggerEvent(string $eventName, StateMachineItemTransfer $stateMachineItemTransfer): int;

    /**
     * @param array $stateMachineItems
     * @return array
     */
    public function getManualEventsForStateMachineItems(array $stateMachineItems): array;

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return array
     */
    public function getManualEventsForStateMachineItem(StateMachineItemTransfer $stateMachineItemTransfer): array;
}
