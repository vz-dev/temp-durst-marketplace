<?php

namespace Pyz\Zed\DeliveryArea\Business\Exception;

use Exception;
use Throwable;

class TimeSlotInvalidMaxProductsValueException extends Exception
{
    const MESSAGE = 'The maximum products value of the time slot has to be greater than 0';

    public function __construct($message = self::MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
