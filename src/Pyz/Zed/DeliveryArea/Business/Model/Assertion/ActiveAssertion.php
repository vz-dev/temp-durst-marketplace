<?php
/**
 * Durst - project - ActiveAssertion.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.10.20
 * Time: 13:20
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;

use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;

class ActiveAssertion implements ConcreteTimeSlotAssertionInterface
{

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity): bool
    {
        return ($concreteTimeSlotEntity->getIsActive() === true);
    }
}
