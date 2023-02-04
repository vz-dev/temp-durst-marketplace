<?php
/**
 * Durst - project - NoWeekDaySetInTimeSlotException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 10.04.18
 * Time: 12:07
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class NoWeekDaySetInTimeSlotException extends \Exception
{
    const MESSAGE = 'There are no week days defined in the given time slot';
}