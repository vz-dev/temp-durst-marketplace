<?php
/**
 * Durst - project - ConcreteTimeSlotDeleteToucherInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.10.20
 * Time: 08:46
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;


interface ConcreteTimeSlotDeleteToucherInterface
{
    /**
     * @param int $idConcreteTimeSlot
     * @param int $idBranch
     * @return void
     */
    public function deleteConcreteTimeSlotByIdAndBranch(
        int $idConcreteTimeSlot,
        int $idBranch
    ): void;
}
