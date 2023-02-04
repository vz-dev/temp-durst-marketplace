<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 03.08.18
 * Time: 11:01
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleNotExistsException extends VehicleException
{
    public const MESSAGE = 'A vehicle with the id %d does not exist';
}
