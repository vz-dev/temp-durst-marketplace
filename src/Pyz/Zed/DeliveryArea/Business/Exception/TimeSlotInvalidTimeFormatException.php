<?php

namespace Pyz\Zed\DeliveryArea\Business\Exception;

use Exception;

class TimeSlotInvalidTimeFormatException extends Exception
{
    const MESSAGE = 'The field "%s" of the time slot has an invalid time format';
}
