<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 12:14
 */

namespace Pyz\Zed\Tour\Business\Exception;


class DrivingLicenceNotExistsException extends DrivingLicenceException
{
    public const MESSAGE = 'A driving licence with the id %d does not exist';
}
