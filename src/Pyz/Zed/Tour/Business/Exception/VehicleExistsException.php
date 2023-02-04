<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 31.07.18
 * Time: 13:00
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleExistsException extends VehicleException
{
    public const MESSAGE = 'A vehicle with the id %d already exists';
}
