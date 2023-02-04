<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 26.09.18
 * Time: 23:27
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class TimeSlotQueryInvalidWeekdayException extends \Exception
{
    public const MESSAGE = 'The given weekday argument is out of range.';
}
