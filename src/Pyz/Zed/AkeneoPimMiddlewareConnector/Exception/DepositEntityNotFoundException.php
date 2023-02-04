<?php
/**
 * Durst - project - DepositEntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.04.18
 * Time: 14:04
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Exception;


class DepositEntityNotFoundException extends \Exception
{
    const MESSAGE = 'The deposit with the code %s could not be found in the database';
}