<?php
/**
 * Durst - project - GoogleApiFacadeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 07:43
 */

namespace Pyz\Zed\GoogleApi\Business;


use Generated\Shared\Transfer\GoogleApiCoordinatesTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

interface GoogleApiFacadeInterface
{
    /**
     * receives a query string usually "address city zipcode" and returns a GoogleApiCoordinatesTransfer with
     * the coresponding latitude and longitude
     *
     * @param string $queryString
     * @param string|null $postcode
     * @return GoogleApiCoordinatesTransfer
     */
    public function getCoordinatesForAddressString(string $queryString, ?string $postcode=null): GoogleApiCoordinatesTransfer;

    /**
     * Calls the geocoder with info provided by Quotetransfer and adds lat & lng to the shipping address
     * before saving the order
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     */
    public function saveLatLngInOrderAddress(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);
}
