<?php
/**
 * Durst - project - StateMachineTourItemReaderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\StateMachineItemTransfer;

interface StateMachineTourItemReaderInterface
{
    /**
     * @param int[] $stateIds
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array;

    /**
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     */
    public function getStateMachineItems(): array;

    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer
     */
    public function getStateMachineItemByIdConcreteTour(int $idConcreteTour): StateMachineItemTransfer;
}
