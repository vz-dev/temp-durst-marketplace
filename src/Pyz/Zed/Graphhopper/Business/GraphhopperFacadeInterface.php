<?php
/**
 * Durst - project - GraphhopperFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 16:06
 */

namespace Pyz\Zed\Graphhopper\Business;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\GraphhopperCoordinatesTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Graphhopper\Business\Exception\OptimizeJobTerminatedException;

interface GraphhopperFacadeInterface
{
    /**
     * receives a query string usually "address city zipcode" and returns a GraphhopperCoordinatesTransfer with
     * the coresponding latitude and longitude
     *
     * @param string $queryString
     * @return GraphhopperCoordinatesTransfer
     */
    public function getCoordinatesForAddressString(string $queryString): GraphhopperCoordinatesTransfer;

    /**
     * Order all stops contained in the graphhoppertransfer by address and delivery timeframe
     * and returns a graphhoppertransfer with all stops in an optimized delivery order
     *
     * Exceptions:
     *  - GenerateOptimizeJobFailException: Thrown when there is a problem creating the graphhopper Optimization job
     *  - OptimizeJobTerminatedException: Thrown when the optimization job is unexpectedly terminated
     *
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     */
    public function orderTourOrders(GraphhopperTourTransfer $graphhopperTourTransfer): GraphhopperTourTransfer;

    /**
     * Calls the geocoder with info provided by Addresstransfer and adds lat & lng to the shipping address
     * before saving the order
     *
     * @param AddressTransfer $addressTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     */
    public function saveLatLngInOrderAddress(AddressTransfer $addressTransfer, SaveOrderTransfer $saveOrderTransfer);
}
