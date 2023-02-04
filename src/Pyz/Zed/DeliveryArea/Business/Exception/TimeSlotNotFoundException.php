<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:52
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class TimeSlotNotFoundException extends \Exception
{
    const NOT_FOUND = 'The time slot with the id %d does not exist';
}