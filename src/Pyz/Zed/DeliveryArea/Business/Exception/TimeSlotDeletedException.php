<?php

namespace Pyz\Zed\DeliveryArea\Business\Exception;

use Exception;

class TimeSlotDeletedException extends Exception
{
    const MESSAGE = 'The time slot with the ID %d has been deleted';
}
