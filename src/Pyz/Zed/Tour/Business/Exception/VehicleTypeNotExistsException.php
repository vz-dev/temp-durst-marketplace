<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 11:18
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleTypeNotExistsException extends VehicleTypeException
{
    public const MESSAGE = 'A vehile type with the id "%d" does not exist';
}
