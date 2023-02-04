<?php
/**
 * Created by PhpStorm.
 * User: Giuliano
 * Date: 29.01.18
 * Time: 17:27
 */

namespace Pyz\Zed\Absence\Business\Exception;


class AbsenceNotFoundException extends \Exception
{
    const NOT_FOUND = 'The absence object with the id %d could not be found';
}