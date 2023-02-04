<?php
/**
 * Durst - project - StateMachineTourItemSaverInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\StateMachineItemTransfer;

interface StateMachineTourItemSaverInterface
{
    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return bool
     */
    public function itemStateUpdate(StateMachineItemTransfer $stateMachineItemTransfer): bool;
}
