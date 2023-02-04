<?php
/**
 * Durst - project - OptimizeJobTerminatedException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.12.19
 * Time: 11:42
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class OptimizeJobTerminatedException extends OptimizeException
{
    public const MESSAGE = 'Der Job für die Tour "%s" wurde unerwartet beendet.';
}
