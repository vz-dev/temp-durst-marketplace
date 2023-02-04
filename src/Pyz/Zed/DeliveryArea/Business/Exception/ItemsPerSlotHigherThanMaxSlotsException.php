<?php
/**
 * Durst - project - ItemsPerSlotHigherThanMaxSlotsException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 03.09.18
 * Time: 13:44
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class ItemsPerSlotHigherThanMaxSlotsException extends \Exception
{
    const MESSAGE = 'The amount of items per timeslot is higher than the amount of max timeslots';
}