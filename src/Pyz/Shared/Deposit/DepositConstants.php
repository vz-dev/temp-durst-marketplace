<?php
/**
 * Durst - project - DepositConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.06.18
 * Time: 12:05
 */

namespace Pyz\Shared\Deposit;


interface DepositConstants
{
    public const DEPOSIT_EXPENSE_TYPE = 'deposit';
    public const DEPOSIT_EXPENSE_NAME = 'Pfand';
    public const DEPOSIT_RETURN_EXPENSE_TYPE = 'RETURNED_DEPOSIT_TYPE';
    public const DEPOSIT_RETURN_EXPENSE_DISPLAY_NAME = 'Pfandrückgabe';
}