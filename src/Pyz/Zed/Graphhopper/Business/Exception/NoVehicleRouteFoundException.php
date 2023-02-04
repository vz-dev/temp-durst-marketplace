<?php
/**
 * Durst - project - NoVehicleRouteFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.12.19
 * Time: 15:30
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class NoVehicleRouteFoundException extends OptimizeException
{
    public const MESSAGE = 'Es wurden keine Routen gefunden';
}
