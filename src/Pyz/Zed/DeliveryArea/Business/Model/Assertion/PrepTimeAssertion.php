<?php
/**
 * Durst - project - PrepTimeAssertion.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 13:38
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;

class PrepTimeAssertion implements ConcreteTimeSlotAssertionInterface
{
    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     * @throws \Exception
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity): bool
    {
        if($concreteTimeSlotEntity->getSpyTimeSlot()->getPrepTime() === null){
            return true;
        }

        $now = new \DateTime('now');
        $start = clone ($concreteTimeSlotEntity->getStartTime());

        // subtract preparation time and check if still a suitable start time
        $start->sub(new \DateInterval('PT' . $concreteTimeSlotEntity->getSpyTimeSlot()->getPrepTime() . 'M'));
        //throw new \Exception('start ' . $start->getTimezone()->getName() . ' - now ' . $now->getTimezone()->getName());

        return $start > $now;
    }
}