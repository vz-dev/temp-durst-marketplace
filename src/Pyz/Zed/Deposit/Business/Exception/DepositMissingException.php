<?php
/**
 * Durst - project - DepositMissingException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.04.18
 * Time: 17:51
 */

namespace Pyz\Zed\Deposit\Business\Exception;


class DepositMissingException extends \Exception
{
    const MESSAGE = 'No deposit found for product with sku %s';
}