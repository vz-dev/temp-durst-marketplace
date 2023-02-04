<?php
/**
 * Durst - merchant_center - ExpenseWithIdNotFoundException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-25
 * Time: 13:19
 */

namespace Pyz\Zed\Sales\Business\Exceptions;


class ExpenseWithIdNotFoundException extends \Exception
{
    public const MESSAGE = 'A expense with the id %d does not exist';
}