<?php
/**
 * Durst - project - NoOrdersForConcreteTourException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-06
 * Time: 16:41
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class NoOrdersForConcreteTourException extends OptimizeException
{
    public const MESSAGE = 'Für die Tour %s gibt es keine Bestellungen';
}
