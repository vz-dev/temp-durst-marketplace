<?php
/**
 * Durst - project - TimeSlotGeneratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.06.21
 * Time: 14:47
 */

namespace Pyz\Zed\GraphMasters\Business\Generator;


interface TimeSlotGeneratorInterface
{
    public function createTimeSlotsForDeliveryAreaCategory();

    public function createTimeSlotsForTimeSlotUntilLimit();
}
