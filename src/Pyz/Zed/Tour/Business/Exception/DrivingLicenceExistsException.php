<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 11:30
 */

namespace Pyz\Zed\Tour\Business\Exception;


class DrivingLicenceExistsException extends DrivingLicenceException
{
    public const MESSAGE = 'A driving licence with the id %d or with the code %s already exists';
    public const ID_EXISTS_MESSAGE = 'A driving licence with the id "%d" already exists';
    public const CODE_EXISTS_MESSAGE = 'A driving licence with the code "%s" already exists';
}
