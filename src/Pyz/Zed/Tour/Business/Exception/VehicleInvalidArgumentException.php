<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 31.07.18
 * Time: 13:06
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleInvalidArgumentException extends VehicleException
{
    public const MESSAGE = 'The number plate of the given vehicle transfer object cannot be null';
}
