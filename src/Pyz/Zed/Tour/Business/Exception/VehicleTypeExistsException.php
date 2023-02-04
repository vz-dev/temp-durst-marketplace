<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 11:48
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleTypeExistsException extends VehicleTypeException
{
    public const MESSAGE = 'Vehicle type already exists';
    public const ID_EXISTS_MESSAGE = 'A vehicle type with the id "%d" already exists';

}
