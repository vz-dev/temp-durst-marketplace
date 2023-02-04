<?php
/**
 * Durst - project - TourToGraphhopperMapperNoBranchCoordinatesException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-09
 * Time: 14:44
 */

namespace Pyz\Zed\Tour\Business\Exception;


use Exception;

class TourToGraphhopperMapperNoBranchCoordinatesException extends Exception
{
    public const MESSAGE = 'There are no Warehouse Coordinates(Lat/lng) for the Branch "%s - id: %s"';
}
