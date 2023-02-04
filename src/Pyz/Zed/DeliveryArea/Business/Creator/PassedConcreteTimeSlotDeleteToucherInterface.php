<?php
/**
 * Durst - project - PassedConcreteTimeSlotDeleteToucherInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.11.18
 * Time: 11:20
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;

interface PassedConcreteTimeSlotDeleteToucherInterface
{
    /**
     * @return void
     */
    public function touchPassedConcreteTimeSlotsToDelete();
}
