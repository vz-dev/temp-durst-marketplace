<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 15.12.17
 * Time: 13:49
 */

namespace Pyz\Zed\MerchantPrice\Business\Exception;


class WrongBranchException extends \Exception
{
    const WRONG_ID_BRANCH_TO_DELETE = 'The price with id %d you are trying to delete does not belong to the currently logged in branch with the id %d';
}