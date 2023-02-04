<?php
/**
 * Durst - project - LatLngOrderAddressSaverInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-16
 * Time: 08:00
 */

namespace Pyz\Zed\Graphhopper\Business\Checkout;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

interface LatLngOrderAddressSaverInterface
{
    /**
     * @param AddressTransfer $addressTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function saveLatLngToAddress(AddressTransfer $addressTransfer, SaveOrderTransfer $saveOrderTransfer);
}
