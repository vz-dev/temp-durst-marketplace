<?php
/**
 * Durst - project - LogEntryNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.02.19
 * Time: 11:45
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;


class LogEntryNotFoundException extends \RuntimeException
{
    public const MESSAGE = 'Could not found %s log entry for order %d with';
}