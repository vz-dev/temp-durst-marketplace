<?php
/**
 * Durst - project - StartAfterEndException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.05.18
 * Time: 12:03
 */

namespace Pyz\Zed\Absence\Business\Exception;


class StartAfterEndException extends \Exception
{
    const MESSAGE = 'End date must be after start date. Start: %s - End: %s';
}