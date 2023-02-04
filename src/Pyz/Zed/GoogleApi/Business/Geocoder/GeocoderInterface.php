<?php
/**
 * Durst - project - GeocoderInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 20:08
 */

namespace Pyz\Zed\GoogleApi\Business\Geocoder;


use Generated\Shared\Transfer\GoogleApiCoordinatesTransfer;

interface GeocoderInterface
{
    /**
     * @param string $addressString
     * @param string|null $postcode
     * @return GoogleApiCoordinatesTransfer
     */
    public function getCoordinatesForAddressString(string $addressString, ?string $postcode): GoogleApiCoordinatesTransfer;
}
