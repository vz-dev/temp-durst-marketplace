<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 24.08.18
 * Time: 14:40
 */

namespace Pyz\Zed\Driver\Business\Exception;


class DriverNotExistsException extends DriverException
{
    public const MESSAGE = 'A driver with the id "%d" does not exist';
    public const EMAIL_MESSAGE = 'A driver with the email "%s" does not exist.';
}
