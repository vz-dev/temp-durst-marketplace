<?php
/**
 * Durst - project - CheapestTimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.11.18
 * Time: 11:02
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot;

use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\TimeSlotKeyResponseInterface as Response;
use stdClass;

class CheapestTimeSlotHydrator implements HydratorInterface
{
    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(
        stdClass $requestObject,
        stdClass $responseObject
    ) {
        $this->setCheapestTimeSlot($responseObject->{Response::KEY_TIME_SLOTS});
    }

    /**
     * @param array $timeSlots
     *
     * @return void
     */
    protected function setCheapestTimeSlot(array $timeSlots)
    {
        if (count($timeSlots) < 1) {
            return;
        }
        $counter = 0;
        $allTimeSlotsSamePrice = true;
        foreach ($timeSlots as $timeSlot) {
            if ($counter === 0) {
                $counter++;
                $cheapest = $timeSlot;
                continue;
            }

            if ($timeSlot->{Response::KEY_TIME_SLOT_TOTAL} < $cheapest->{Response::KEY_TIME_SLOT_TOTAL}) {
                $cheapest = $timeSlot;
                $allTimeSlotsSamePrice = false;
            }
        }

        if (!$allTimeSlotsSamePrice) {
            $cheapest->{Response::KEY_TIME_SLOT_CHEAPEST_SLOT} = true;
        }
    }
}
