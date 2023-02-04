<?php
/**
 * Durst - project - OptimizeNoBranchCoordnatesException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-16
 * Time: 09:40
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class OptimizeNoBranchCoordinatesException extends OptimizeException
{
    public const MESSAGE = 'Es wurden keine Koordinaten für den Branch "%s" gefunden.';
}
