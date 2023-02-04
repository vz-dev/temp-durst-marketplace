<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 11:31
 */

namespace Pyz\Zed\Tour\Business\Exception;


class DrivingLicenceInvalidArgumentException extends DrivingLicenceException
{
    public const MESSAGE = 'The code of the given driving licence transfer object cannot be null';
}
