<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 13:54
 */

namespace Pyz\Zed\TermsOfService\Business\Exception;


class TermsOfServiceNotFoundException extends \Exception
{
    const NOT_FOUND_BY_ID = 'The terms of service object with the id %d you are trying to delete does not exist';
}