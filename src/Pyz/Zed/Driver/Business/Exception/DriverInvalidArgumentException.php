<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:46
 */

namespace Pyz\Zed\Driver\Business\Exception;


class DriverInvalidArgumentException extends DriverException
{
    public const MESSAGE = 'Driver object transfer is invalid';
    public const NO_FK_BRANCH_MESSAGE = 'The fkBranch of a Driver must not be null';
}
