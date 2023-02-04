<?php
/**
 * Durst - project - ConcreteTimeSlotCreatorInterfac.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.11.18
 * Time: 13:37
 */

namespace Pyz\Zed\DeliveryArea\Business\Creator;

interface ConcreteTimeSlotCreatorInterface
{
    /**
     * @return void
     */
    public function createConcreteTimeSlots();
}
