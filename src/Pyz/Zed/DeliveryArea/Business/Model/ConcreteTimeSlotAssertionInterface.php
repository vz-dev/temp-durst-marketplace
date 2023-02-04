<?php
/**
 * Durst - project - ConcreteTimeSlotAssertionInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 13:23
 */

namespace Pyz\Zed\DeliveryArea\Business\Model;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;

interface ConcreteTimeSlotAssertionInterface
{
    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity) : bool;
}