<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.01.18
 * Time: 09:15
 */

namespace Pyz\Zed\TermsOfService\Business\Exception;


class TermsOfServiceAlreadyAcceptedException extends \Exception
{
    const ALREADY_ACCEPTED = 'The terms of service #%d have already been accepted by the merchant #%d';
}