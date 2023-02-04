<?php
/**
 * Durst - project - GeocodingInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 16:11
 */

namespace Pyz\Zed\Graphhopper\Business\Model;


use Generated\Shared\Transfer\GraphhopperCoordinatesTransfer;

interface GeocodingInterface
{
    /**
     * @param string $addressString
     * @return GraphhopperCoordinatesTransfer
     */
    public function getCoordinatesForAddressString(string $addressString): GraphhopperCoordinatesTransfer;
}
