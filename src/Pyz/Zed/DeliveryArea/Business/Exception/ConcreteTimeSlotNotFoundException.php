<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:52
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


class ConcreteTimeSlotNotFoundException extends \Exception
{
    const NOT_FOUND = 'The concrete time slot with the id %d does not exist';
}