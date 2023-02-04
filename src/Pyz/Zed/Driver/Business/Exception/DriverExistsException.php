<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:39
 */

namespace Pyz\Zed\Driver\Business\Exception;


class DriverExistsException extends DriverException
{
    public const MESSAGE = 'Driver already exists';
    public const ID_EXISTS_MESSAGE = 'A driver with the id "%d" already exists';
}
